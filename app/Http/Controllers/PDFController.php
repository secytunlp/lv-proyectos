<?php

namespace App\Http\Controllers;

use PDF;

class PDFController extends Controller
{
    public function generateAltaPDF()
    {
        $data = [
            'facultad' => 'Facultad de Ingeniería',
            'codigo' => '12345',
            'duracion' => '2 años',
            'denominacion' => 'Proyecto de Investigación XYZ',
            'fecha_inicio' => '01/01/2023',
            'fecha_fin' => '31/12/2024',
            'director' => 'Dr. Juan Pérez',
            'integrante' => 'Ing. María García',
            'cuil' => '20-12345678-9',
            'categoria_spu' => 'Titular',
            'categoria_sicadi' => 'Investigador Principal',
            'cargo_docente' => 'Profesor Asociado',
            'dedicacion_docente' => 'Tiempo Completo',
            'dedicacion_proyecto' => '50%',
        ];

        $pdf = PDF::loadView('integrantes.alta', $data);

        return $pdf->download('alta.pdf');
    }
}

