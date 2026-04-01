<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class VerificarHistorialSicadi extends Command
{
    protected $signature = 'sicadi:verificar-historial';
    protected $description = 'Categorias en investigador_sicadis que no existen en solicitud_sicadis';

    public function handle()
    {
        $this->info('Buscando categorías en historial que no están en solicitudes...');

        $data = DB::table('investigadors as i')
            ->join('personas as p', 'p.id', '=', 'i.persona_id')
            ->join('sicadis as s', 's.id', '=', 'i.sicadi_id')
            ->leftJoin('solicitud_sicadis as ss', function ($join) {
                $join->on('ss.cuil', '=', 'p.cuil')
                    ->whereRaw('UPPER(TRIM(ss.categoria_asignada)) = UPPER(TRIM(s.nombre))');
            })
            ->where('i.sicadi_id', '!=', 1) // <-- excluir sin categoria
            ->whereNull('ss.id')
            ->select(
                'i.id as investigador_id',
                'p.cuil',
                'p.apellido',
                'p.nombre',
                's.nombre as categoria'
            )
            ->get();

        if ($data->isEmpty()) {
            $this->info('No se encontraron diferencias.');
            return Command::SUCCESS;
        }

        // Crear Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Apellido');
        $sheet->setCellValue('C1', 'Nombre');
        $sheet->setCellValue('D1', 'CUIL');
        $sheet->setCellValue('E1', 'Categoria');

        // Datos
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item->investigador_id);
            $sheet->setCellValue("B{$row}", $item->apellido);
            $sheet->setCellValue("C{$row}", $item->nombre);
            $sheet->setCellValue("D{$row}", $item->cuil);
            $sheet->setCellValue("E{$row}", $item->categoria);
            $row++;
        }

        // Guardar archivo
        $filename = 'sicadi_verificacion_' . now()->format('Ymd_His') . '.xlsx';
        $path = storage_path("app/{$filename}");

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $this->info("Excel generado:");
        $this->info($path);

        return Command::SUCCESS;
    }
}
