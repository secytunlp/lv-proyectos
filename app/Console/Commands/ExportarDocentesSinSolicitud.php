<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Exporta a .xlsx las filas de `cargos_alfabetico` con escalafon Docente y/o
 * Docente Preuniversitario cuyo documento NO aparece en `solicitud_sicadis`.
 *
 * Sale TODO: una fila por registro del alfabetico, sin filtrar situaciones ni
 * dependencias. Si una persona tiene tres cargos, salen los tres. Elegir con
 * cual quedarse es una decision posterior, no la toma este comando.
 *
 * El cruce es por documento: se normaliza a digitos sin ceros a la izquierda,
 * de los dos lados. De `solicitud_sicadis` se toma `documento` y, ademas, el DNI
 * que esta adentro del `cuil` (posiciones 3..10), asi tambien matchean las
 * solicitudes que quedaron con el documento vacio o mal cargado.
 *
 * Uso:
 *   php artisan exportar:docentes-sin-solicitud
 *   php artisan exportar:docentes-sin-solicitud --convocatoria=5
 *   php artisan exportar:docentes-sin-solicitud --facultad=170 --facultad=181
 *   php artisan exportar:docentes-sin-solicitud --salida=storage/app/faltantes.xlsx
 */
class ExportarDocentesSinSolicitud extends Command
{
    protected $signature = 'exportar:docentes-sin-solicitud
        {--escalafon=* : Escalafones a incluir. Por defecto "Docente" y "Docente Preuniversitario"}
        {--convocatoria= : Id de convocatoria. Si se indica, solo cuentan las solicitudes de esa convocatoria}
        {--facultad=* : Filtra por cd_facultad. Vacio = todas}
        {--excluir-excel=* : Ruta de un .xls/.xlsx/.csv con gente a sacar del listado. Se puede repetir}
        {--control-nombre : Marca a los que, pese a no cruzar por CUIL, tienen una solicitud con el mismo apellido y nombre}
        {--excluir-nombre : Ademas de marcarlos, saca del listado a los de coincidencia EXACTA. Implica --control-nombre}
        {--sin-categoria : Deja solo a los que NO tienen investigadors.categoria_id en la lista de --categorias}
        {--categorias=6,7,8,9,10 : Ids de categoria que cuentan como "categorizado", separados por coma}
        {--salida= : Ruta del .xlsx de salida}';

    protected $description = 'Exporta a Excel los cargos docentes de cargos_alfabetico cuyo DNI no figura en solicitud_sicadis';

    /** Escalafones que se toman si no se pasa --escalafon */
    private const ESCALAFONES = ['Docente', 'Docente Preuniversitario'];

    private const DEDDOC = [
        1 => 'Exclusiva',
        2 => 'Semi Exclusiva',
        3 => 'Simple',
    ];

    private const HEADERS = [
        'DNI', 'Apellido y Nombres', 'Nacimiento', 'Escalafon', 'Dependencia',
        'cd_facultad', 'Cargo', 'Clase', 'Dedicacion', 'Funcion', 'Situacion', 'Desde',
        'En_investigadors', 'categoria_id', 'Categoria',
    ];

    /** Se agregan al final solo con --control-nombre */
    private const HEADERS_NOMBRE = ['Match_nombre', 'Solicitud_coincidente'];

    /** Columnas que van centradas */
    private const CENTRADAS = ['A', 'C', 'F', 'H', 'L', 'M', 'N'];

    /** clave de documento => array de categoria_id de investigadors (vacio = esta pero sin categoria) */
    private $categoriaPorDoc = null;

    /** id => nombre de la tabla categorias */
    private $nombresCategoria = null;

    /** array('exacto' => [...], 'parcial' => [...]) con las solicitudes indexadas por nombre */
    private $nombresSolicitud = null;

    public function handle(): int
    {
        $escalafones = $this->option('escalafon');
        if (empty($escalafones)) {
            $escalafones = self::ESCALAFONES;
        }

        $this->info('=== Cargos docentes sin solicitud en solicitud_sicadis ===');
        $this->line('Escalafones: ' . implode(' | ', $escalafones));

        $conSolicitud = $this->documentosConSolicitud();
        if ($conSolicitud === null) {
            return self::FAILURE;
        }

        $filas = $this->cargosDocentes($escalafones);
        if ($filas->isEmpty()) {
            $this->warn('No hay filas en cargos_alfabetico con esos escalafones.');
            $this->mostrarEscalafonesDisponibles();
            return self::FAILURE;
        }
        $this->line('Filas de cargos_alfabetico: ' . $filas->count());

        $sinDni = 0;
        $faltantes = $filas->filter(function ($c) use ($conSolicitud, &$sinDni) {
            $clave = $this->claveDoc($c->dni);
            if ($clave === '') {
                $sinDni++;
                return false;
            }
            return !isset($conSolicitud[$clave]);
        })->values();

        if ($sinDni > 0) {
            $this->warn($sinDni . ' fila(s) con documento vacio o no numerico: quedan afuera.');
        }

        $personasTotal    = $this->personasDistintas($filas);
        $personasFaltante = $this->personasDistintas($faltantes);

        $this->newLine();
        $this->info('Personas distintas en el alfabetico: ' . $personasTotal);
        $this->info('Sin solicitud en solicitud_sicadis:  ' . $personasFaltante
            . ' personas / ' . $faltantes->count() . ' cargos');

        $excluidos = $this->documentosDeExcels();
        if ($excluidos === null) {
            return self::FAILURE;
        }
        if (count($excluidos) > 0) {
            $antes = $faltantes->count();
            $faltantes = $faltantes->filter(function ($c) use ($excluidos) {
                $k = $this->claveDoc($c->dni);
                return $k !== '' && !isset($excluidos[$k]);
            })->values();

            $this->newLine();
            $this->info('Filtro --excluir-excel:');
            $this->line('  Se descartan ' . ($antes - $faltantes->count()) . ' cargos');
            $this->info('  Quedan: ' . $this->personasDistintas($faltantes)
                . ' personas / ' . $faltantes->count() . ' cargos');
        }

        // La categoria se carga siempre: aunque no se filtre por ella, va como
        // columna para poder mirarla desde el Excel.
        $categorias = $this->categoriasBuscadas();
        $this->cargarCategorias();

        if ($this->option('sin-categoria')) {
            $antes = $faltantes->count();
            $faltantes = $faltantes->filter(function ($c) use ($categorias) {
                return !$this->tieneCategoria($c->dni, $categorias);
            })->values();

            $this->newLine();
            $this->info('Filtro --sin-categoria (categoria_id NOT IN ' . implode(',', $categorias) . '):');
            $this->line('  Se descartan ' . ($antes - $faltantes->count()) . ' cargos ya categorizados');
            $this->info('  Quedan: ' . $this->personasDistintas($faltantes)
                . ' personas / ' . $faltantes->count() . ' cargos');
        }

        if ($this->controlNombre()) {
            $this->cargarIndiceNombres();

            $exactos = 0;
            $parciales = 0;
            foreach ($faltantes as $c) {
                $m = $this->matchPorNombre($c->investigador);
                if ($m['tipo'] === 'EXACTO') {
                    $exactos++;
                } elseif ($m['tipo'] === 'PARCIAL') {
                    $parciales++;
                }
            }

            $this->newLine();
            $this->info('Control por apellido y nombre:');
            $this->line('  Coincidencia EXACTA  (apellido + todos los nombres): ' . $exactos . ' cargos');
            $this->line('  Coincidencia PARCIAL (apellido + primer nombre):     ' . $parciales . ' cargos');
            $this->line('  Son personas que NO cruzaron por CUIL pero figuran con ese nombre en');
            $this->line('  solicitud_sicadis: o el CUIL esta mal cargado en algun lado, o son homonimos.');

            if ($this->option('excluir-nombre')) {
                $antes = $faltantes->count();
                $faltantes = $faltantes->filter(function ($c) {
                    return $this->matchPorNombre($c->investigador)['tipo'] !== 'EXACTO';
                })->values();
                $this->newLine();
                $this->info('Filtro --excluir-nombre (solo los EXACTO):');
                $this->line('  Se descartan ' . ($antes - $faltantes->count()) . ' cargos');
                $this->info('  Quedan: ' . $this->personasDistintas($faltantes)
                    . ' personas / ' . $faltantes->count() . ' cargos');
                $this->warn('  Los PARCIAL quedan en el listado, marcados en la columna Match_nombre.');
            }
        }

        if ($faltantes->isEmpty()) {
            $this->warn('No hay nada para exportar.');
            return self::SUCCESS;
        }

        $sufijo = $this->option('sin-categoria') ? '_sin_categoria' : '';
        $salida = $this->option('salida') ?: storage_path(
            'app/docentes_sin_solicitud' . $sufijo . '_' . date('Ymd') . '.xlsx'
        );
        $this->ensureDir(dirname($salida));

        $this->escribir($faltantes, $salida);
        $this->resumen($faltantes);

        $this->newLine();
        $this->info('Listo: ' . $salida);
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Lados del cruce
    // -------------------------------------------------------------------------

    /**
     * Set de documentos normalizados que YA tienen solicitud. Devuelve null si
     * la tabla no se puede leer.
     */
    private function documentosConSolicitud(): ?array
    {
        // `documento` esta en el $fillable del modelo pero no existe en todas
        // las bases; el CUIL si. Se usa solo si la columna esta.
        $hayDocumento = Schema::hasColumn('solicitud_sicadis', 'documento');

        $columnas = $hayDocumento ? ['cuil', 'documento'] : ['cuil'];
        $query = DB::table('solicitud_sicadis')->select($columnas);

        $this->line($hayDocumento
            ? 'Cruce por: cuil + documento'
            : 'Cruce por: cuil (la tabla no tiene columna documento)');

        $convocatoria = $this->option('convocatoria');
        if ($convocatoria !== null && $convocatoria !== '') {
            $query->where('convocatoria_id', $convocatoria);
            $this->line('Convocatoria: solo la ' . $convocatoria);
        } else {
            $this->line('Convocatoria: todas');
        }

        try {
            $solicitudes = $query->get();
        } catch (\Exception $e) {
            $this->error('No pude leer solicitud_sicadis: ' . $e->getMessage());
            return null;
        }

        $set = [];
        $sinDocumento = 0;
        $porCuil = 0;

        // Se agregan las dos claves, la del documento y la del CUIL: si estan
        // cargadas distintas, cualquiera de las dos vale como "ya se presento".
        foreach ($solicitudes as $s) {
            $doc  = $hayDocumento ? $this->claveDoc($s->documento) : '';
            $cuil = $this->dniDesdeCuil($s->cuil);

            if ($doc !== '') {
                $set[$doc] = true;
            }
            if ($cuil !== '') {
                $set[$cuil] = true;
                if ($doc === '') {
                    $porCuil++;
                }
            }
            if ($doc === '' && $cuil === '') {
                $sinDocumento++;
            }
        }

        $this->line('Solicitudes leidas: ' . $solicitudes->count()
            . ' -> ' . count($set) . ' documentos distintos');
        if ($hayDocumento && $porCuil > 0) {
            $this->line('  ' . $porCuil . ' identificada(s) solo por el CUIL (documento vacio)');
        }
        if ($sinDocumento > 0) {
            $this->warn('  ' . $sinDocumento . ' solicitud(es) sin CUIL usable'
                . ($hayDocumento ? ' ni documento' : '') . ': no se pueden cruzar'
                . ' (esa gente puede aparecer en el listado aunque se haya presentado).');
        }

        return $set;
    }

    /**
     * Filas de cargos_alfabetico. Sin filtro de situacion: entran tambien
     * Renuncia, Jubilacion y las licencias.
     */
    private function cargosDocentes(array $escalafones)
    {
        $query = DB::table('cargos_alfabetico')->whereIn('escalafon', $escalafones);

        $facultades = $this->option('facultad');
        if (!empty($facultades)) {
            $query->whereIn('cd_facultad', $facultades);
            $this->line('Dependencias: ' . implode(', ', $facultades));
        } else {
            $this->line('Dependencias: todas');
        }
        $this->line('Situaciones: todas (no se excluye ninguna)');

        return $query
            ->orderBy('investigador')
            ->orderBy('dni')
            ->orderBy('cd_deddoc')
            ->orderBy('cd_cargo')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Control por apellido y nombre
    // -------------------------------------------------------------------------

    /**
     * Indices de solicitud_sicadis por nombre, para detectar a los que no
     * cruzaron por CUIL pero igual se presentaron (CUIL mal cargado de un lado
     * o del otro).
     *
     * Se arman dos:
     *   exacto  = APELLIDO|TODOS LOS NOMBRES
     *   parcial = APELLIDO|PRIMER NOMBRE
     *
     * El parcial existe porque es comun que en un lado figure "JUAN CARLOS" y en
     * el otro solo "JUAN". Trae homonimos: es para revisar, no para descartar.
     */
    private function cargarIndiceNombres(): void
    {
        if ($this->nombresSolicitud !== null) {
            return;
        }

        $query = DB::table('solicitud_sicadis as s')
            ->leftJoin('sicadi_convocatorias as c', 'c.id', '=', 's.convocatoria_id')
            ->select('s.id', 's.apellido', 's.nombre', 's.cuil', 'c.tipo', 'c.year');

        $convocatoria = $this->option('convocatoria');
        if ($convocatoria !== null && $convocatoria !== '') {
            $query->where('s.convocatoria_id', $convocatoria);
        }

        $exacto  = array();
        $parcial = array();

        foreach ($query->get() as $s) {
            $ap = $this->normNombre($s->apellido);
            $no = $this->normNombre($s->nombre);
            if ($ap === '' || $no === '') {
                continue;
            }

            $detalle = '#' . $s->id
                . ' ' . trim((string) $s->apellido) . ', ' . trim((string) $s->nombre)
                . ' [cuil ' . ($s->cuil === null || $s->cuil === '' ? 's/d' : $s->cuil) . ']'
                . ($s->tipo === null ? '' : ' ' . $s->tipo . ' ' . $s->year);

            $kExacto = $ap . '|' . $no;
            if (!isset($exacto[$kExacto])) {
                $exacto[$kExacto] = array();
            }
            $exacto[$kExacto][] = $detalle;

            $partes = explode(' ', $no);
            $kParcial = $ap . '|' . $partes[0];
            if (!isset($parcial[$kParcial])) {
                $parcial[$kParcial] = array();
            }
            $parcial[$kParcial][] = $detalle;
        }

        $this->nombresSolicitud = array('exacto' => $exacto, 'parcial' => $parcial);
        $this->line('Indice por nombre: ' . count($exacto) . ' apellido+nombre distintos');
    }

    /**
     * Devuelve array('tipo' => 'EXACTO'|'PARCIAL'|'', 'detalle' => string) para
     * una fila del alfabetico.
     */
    private function matchPorNombre($completo): array
    {
        $vacio = array('tipo' => '', 'detalle' => '');

        list($ap, $no) = $this->partirNombre($completo);
        if ($ap === '' || $no === '') {
            return $vacio;
        }

        $k = $ap . '|' . $no;
        if (isset($this->nombresSolicitud['exacto'][$k])) {
            return array(
                'tipo'    => 'EXACTO',
                'detalle' => implode(' ;; ', $this->nombresSolicitud['exacto'][$k]),
            );
        }

        $partes = explode(' ', $no);
        $k = $ap . '|' . $partes[0];
        if (isset($this->nombresSolicitud['parcial'][$k])) {
            return array(
                'tipo'    => 'PARCIAL',
                'detalle' => implode(' ;; ', $this->nombresSolicitud['parcial'][$k]),
            );
        }

        return $vacio;
    }

    /**
     * "APELLIDO, NOMBRES" -> array(apellido, nombres) normalizados.
     *
     * Se corta por la coma, no por el primer espacio: asi no se rompen los
     * apellidos compuestos ("DI GIORGIO, ANA" -> DI GIORGIO / ANA).
     */
    private function partirNombre($completo): array
    {
        $s = trim((string) $completo);
        $pos = mb_strpos($s, ',');
        if ($pos === false) {
            return array($this->normNombre($s), '');
        }
        return array(
            $this->normNombre(mb_substr($s, 0, $pos)),
            $this->normNombre(mb_substr($s, $pos + 1)),
        );
    }

    /** Mayusculas, sin acentos, sin puntuacion, espacios colapsados. */
    private function normNombre($v): string
    {
        $v = mb_strtoupper(trim((string) $v), 'UTF-8');
        $v = strtr($v, array(
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Ã' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Õ' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ñ' => 'N', 'Ç' => 'C',
        ));
        $v = preg_replace('/[^A-Z ]/', ' ', $v);
        return trim(preg_replace('/\s+/', ' ', $v));
    }

    // -------------------------------------------------------------------------
    // Exclusiones que vienen en planillas sueltas
    // -------------------------------------------------------------------------

    /**
     * Junta los documentos de los .xlsx / .csv pasados en --excluir-excel.
     *
     * Busca en las primeras 20 filas un encabezado con CUIL, Documento o DNI y
     * usa esa(s) columna(s); si no lo encuentra, avisa y usa la primera columna.
     * Sirve igual con CUIL de 11 digitos o con DNI pelado.
     *
     * Devuelve null si algun archivo no se pudo leer.
     */
    private function documentosDeExcels(): ?array
    {
        $rutas = $this->option('excluir-excel');
        if (empty($rutas)) {
            return array();
        }

        $titulos = array('cuil', 'documento', 'dni', 'nro documento', 'nro. documento');
        $set = array();

        foreach ($rutas as $ruta) {
            if (!is_readable($ruta)) {
                $this->error('No puedo leer el archivo: ' . $ruta);
                return null;
            }

            try {
                $reader = IOFactory::createReaderForFile($ruta);
                $reader->setReadDataOnly(true);
                $hoja = $reader->load($ruta)->getSheet(0);
                $filas = $hoja->toArray(null, true, false, false);
            } catch (\Exception $e) {
                $this->error('No pude abrir ' . $ruta . ': ' . $e->getMessage());
                return null;
            }

            // encabezado
            $idxCab = null;
            $cols = array();
            foreach ($filas as $i => $fila) {
                foreach ($fila as $col => $titulo) {
                    $t = mb_strtolower(trim(preg_replace('/\s+/u', ' ',
                        str_replace("\xc2\xa0", ' ', (string) $titulo))), 'UTF-8');
                    if (in_array($t, $titulos, true)) {
                        $cols[] = $col;
                    }
                }
                if (count($cols) > 0) {
                    $idxCab = $i;
                    break;
                }
                if ($i > 20) {
                    break;
                }
            }

            if ($idxCab === null) {
                $this->warn('  ' . basename($ruta)
                    . ': no encontre encabezado CUIL/Documento/DNI, uso la primera columna.');
                $idxCab = -1;
                $cols = array(0);
            }

            $antes = count($set);
            $vacias = 0;
            for ($i = $idxCab + 1; $i < count($filas); $i++) {
                $huboAlgo = false;
                foreach ($cols as $col) {
                    if (!isset($filas[$i][$col])) {
                        continue;
                    }
                    $v = $filas[$i][$col];
                    if ($v === null || trim((string) $v) === '') {
                        continue;
                    }
                    $huboAlgo = true;

                    // Puede ser un CUIL de 11 o un DNI pelado: se prueban los dos.
                    $k = $this->dniDesdeCuil($v);
                    if ($k === '') {
                        $k = $this->claveDoc($v);
                    }
                    if ($k !== '') {
                        $set[$k] = true;
                    }
                }
                if (!$huboAlgo) {
                    $vacias++;
                }
            }

            $this->line('Exclusion ' . basename($ruta) . ': '
                . (count($set) - $antes) . ' documentos nuevos'
                . ($vacias > 0 ? ' (' . $vacias . ' filas vacias salteadas)' : ''));
        }

        $this->line('Total a excluir por planilla: ' . count($set) . ' documentos');
        return $set;
    }

    // -------------------------------------------------------------------------
    // Categoria en investigadors
    // -------------------------------------------------------------------------

    /** Ids de categoria que cuentan como "ya categorizado". */
    private function categoriasBuscadas(): array
    {
        $crudo = (string) $this->option('categorias');
        $ids = array();
        foreach (explode(',', $crudo) as $p) {
            $p = trim($p);
            if ($p !== '' && ctype_digit($p)) {
                $ids[] = (int) $p;
            }
        }
        return $ids;
    }

    /**
     * Mapa documento normalizado => array de categoria_id, armado desde
     * investigadors + personas. Se indexa por el documento y tambien por el DNI
     * que sale del CUIL de la persona, porque en el alfabetico solo hay DNI y
     * hay personas con uno de los dos campos mal cargado.
     *
     * Una clave presente con array vacio = la persona esta en investigadors pero
     * sin categoria_id. Una clave ausente = no esta en investigadors. Los dos
     * casos son "sin categoria" para el filtro.
     */
    private function cargarCategorias(): void
    {
        if ($this->categoriaPorDoc !== null) {
            return;
        }

        $filas = DB::table('investigadors as i')
            ->join('personas as p', 'p.id', '=', 'i.persona_id')
            ->select('p.documento', 'p.cuil', 'i.categoria_id')
            ->get();

        $mapa = array();
        foreach ($filas as $f) {
            $claves = array();
            $d = $this->claveDoc($f->documento);
            if ($d !== '') {
                $claves[] = $d;
            }
            $c = $this->dniDesdeCuil($f->cuil);
            if ($c !== '' && $c !== $d) {
                $claves[] = $c;
            }

            foreach ($claves as $k) {
                if (!isset($mapa[$k])) {
                    $mapa[$k] = array();
                }
                if ($f->categoria_id !== null && (int) $f->categoria_id > 0) {
                    $cat = (int) $f->categoria_id;
                    if (!in_array($cat, $mapa[$k], true)) {
                        $mapa[$k][] = $cat;
                    }
                }
            }
        }

        $this->categoriaPorDoc = $mapa;
        $this->line('Investigadores leidos: ' . $filas->count()
            . ' -> ' . count($mapa) . ' documentos con ficha en investigadors');

        try {
            $this->nombresCategoria = DB::table('categorias')->pluck('nombre', 'id')->toArray();
        } catch (\Exception $e) {
            $this->nombresCategoria = array();
        }
    }

    /** true si esa persona tiene alguna de las categorias buscadas. */
    private function tieneCategoria($dni, array $categorias): bool
    {
        $k = $this->claveDoc($dni);
        if ($k === '' || !isset($this->categoriaPorDoc[$k])) {
            return false;
        }
        return count(array_intersect($this->categoriaPorDoc[$k], $categorias)) > 0;
    }

    /** 'S' si la persona esta en investigadors, 'N' si no. */
    private function enInvestigadors($dni): string
    {
        $k = $this->claveDoc($dni);
        return ($k !== '' && isset($this->categoriaPorDoc[$k])) ? 'S' : 'N';
    }

    /** categoria_id de la persona, separados por / si tuviera mas de uno. */
    private function categoriaIds($dni): string
    {
        $k = $this->claveDoc($dni);
        if ($k === '' || empty($this->categoriaPorDoc[$k])) {
            return '';
        }
        return implode(' / ', $this->categoriaPorDoc[$k]);
    }

    /** Nombre(s) de la categoria segun la tabla categorias. */
    private function categoriaNombres($dni): string
    {
        $k = $this->claveDoc($dni);
        if ($k === '' || empty($this->categoriaPorDoc[$k])) {
            return '';
        }
        $nombres = array();
        foreach ($this->categoriaPorDoc[$k] as $id) {
            $nombres[] = isset($this->nombresCategoria[$id])
                ? (string) $this->nombresCategoria[$id]
                : ('#' . $id);
        }
        return implode(' / ', $nombres);
    }

    // -------------------------------------------------------------------------
    // Escritura
    // -------------------------------------------------------------------------

    /** true si hay que correr el control por nombre. */
    private function controlNombre(): bool
    {
        return (bool) $this->option('control-nombre') || (bool) $this->option('excluir-nombre');
    }

    private function cabeceras(): array
    {
        return $this->controlNombre()
            ? array_merge(self::HEADERS, self::HEADERS_NOMBRE)
            : self::HEADERS;
    }

    private function escribir($faltantes, string $path): void
    {
        $headers = $this->cabeceras();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sin solicitud');

        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        $r = 2;
        foreach ($faltantes as $c) {
            $sheet->setCellValueExplicitByColumnAndRow(
                1, $r, (string) $c->dni, DataType::TYPE_STRING
            );
            $sheet->setCellValueByColumnAndRow(2,  $r, trim((string) $c->investigador));
            $sheet->setCellValueByColumnAndRow(3,  $r, $this->fechaCorta($c->nacimiento));
            $sheet->setCellValueByColumnAndRow(4,  $r, (string) $c->escalafon);
            $sheet->setCellValueByColumnAndRow(5,  $r, (string) $c->ds_facultad);
            $sheet->setCellValueByColumnAndRow(6,  $r, (string) $c->cd_facultad);
            $sheet->setCellValueByColumnAndRow(7,  $r, (string) $c->ds_cargo);
            $sheet->setCellValueByColumnAndRow(8,  $r, (string) $c->clase);
            $sheet->setCellValueByColumnAndRow(9,  $r, $this->deddoc($c->cd_deddoc));
            $sheet->setCellValueByColumnAndRow(10, $r, (string) $c->funcion);
            $sheet->setCellValueByColumnAndRow(11, $r, (string) $c->situacion);
            $sheet->setCellValueByColumnAndRow(12, $r, $this->fechaCorta($c->dt_fecha));
            $sheet->setCellValueByColumnAndRow(13, $r, $this->enInvestigadors($c->dni));
            $sheet->setCellValueByColumnAndRow(14, $r, $this->categoriaIds($c->dni));
            $sheet->setCellValueByColumnAndRow(15, $r, $this->categoriaNombres($c->dni));
            if ($this->controlNombre()) {
                $m = $this->matchPorNombre($c->investigador);
                $sheet->setCellValueByColumnAndRow(16, $r, $m['tipo']);
                $sheet->setCellValueByColumnAndRow(17, $r, $m['detalle']);
            }
            $r++;
        }

        $this->estilar($sheet, $r - 1);
        (new Xlsx($spreadsheet))->save($path);
        $this->line('Escrito: ' . $path . ' (' . count($faltantes) . ' filas)');

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    }

    private function estilar($sheet, int $lastRow): void
    {
        $headers = $this->cabeceras();
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));

        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}{$lastRow}");

        for ($c = 1; $c <= count(self::HEADERS); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        foreach (self::CENTRADAS as $col) {
            $sheet->getStyle("{$col}2:{$col}{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    // -------------------------------------------------------------------------
    // Resumen por consola
    // -------------------------------------------------------------------------

    private function resumen($faltantes): void
    {
        $this->newLine();
        $this->info('Por escalafon:');
        foreach ($faltantes->groupBy('escalafon')->sortKeys() as $k => $g) {
            $this->line(sprintf('  %-32s %5d', $k === '' ? '(vacio)' : $k, $g->count()));
        }

        $this->newLine();
        $this->info('Por situacion:');
        $porSit = $faltantes->groupBy('situacion')->map(function ($g) {
            return $g->count();
        })->sortDesc();
        foreach ($porSit as $sit => $n) {
            $this->line(sprintf('  %-32s %5d', $sit === '' ? '(vacia)' : $this->corta($sit, 30), $n));
        }

        $this->newLine();
        $this->info('Por dependencia (top 20):');
        $porDep = $faltantes->groupBy('ds_facultad')->map(function ($g) {
            return $g->count();
        })->sortDesc()->take(20);
        foreach ($porDep as $dep => $n) {
            $this->line(sprintf('  %-52s %5d', $this->corta($dep, 50), $n));
        }

        $this->newLine();
        $this->info('Por categoria en investigadors:');
        $porCat = $faltantes->groupBy(function ($c) {
            if ($this->enInvestigadors($c->dni) === 'N') {
                return '(no esta en investigadors)';
            }
            $ids = $this->categoriaIds($c->dni);
            if ($ids === '') {
                return '(sin categoria_id)';
            }
            return $ids . ' = ' . $this->categoriaNombres($c->dni);
        })->map(function ($g) {
            return $g->count();
        })->sortDesc();
        foreach ($porCat as $cat => $n) {
            $this->line(sprintf('  %-32s %5d', $cat, $n));
        }

        $conVarios = $faltantes->groupBy(function ($c) {
            return $this->claveDoc($c->dni);
        })->filter(function ($g) {
            return $g->count() > 1;
        })->count();
        if ($conVarios > 0) {
            $this->newLine();
            $this->line($conVarios . ' persona(s) aparecen con mas de un cargo: salen todas sus filas.');
        }
    }

    private function mostrarEscalafonesDisponibles(): void
    {
        $vals = DB::table('cargos_alfabetico')
            ->select('escalafon', DB::raw('COUNT(*) as n'))
            ->groupBy('escalafon')->orderByDesc('n')->get();

        if ($vals->isEmpty()) {
            return;
        }
        $this->newLine();
        $this->line('Escalafones que hay en la tabla:');
        foreach ($vals as $v) {
            $this->line(sprintf('  %-32s %6d', $v->escalafon, $v->n));
        }
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function personasDistintas($filas): int
    {
        $set = [];
        foreach ($filas as $c) {
            $k = $this->claveDoc($c->dni);
            if ($k !== '') {
                $set[$k] = true;
            }
        }
        return count($set);
    }

    /** Documento normalizado: solo digitos, sin ceros a la izquierda. */
    private function claveDoc($v): string
    {
        $d = ltrim(preg_replace('/\D/', '', (string) $v), '0');
        return strlen($d) >= 6 ? $d : '';
    }

    /**
     * DNI que sale del campo `cuil`. Con 11 digitos es un CUIL y el DNI son las
     * posiciones 3 a 10; con 7 u 8 digitos ya quedo cargado el DNI pelado, que
     * tambien pasa (hay solicitudes asi).
     */
    private function dniDesdeCuil($v): string
    {
        $d = preg_replace('/\D/', '', (string) $v);
        if (strlen($d) === 11) {
            return $this->claveDoc(substr($d, 2, 8));
        }
        if (strlen($d) >= 7 && strlen($d) <= 8) {
            return $this->claveDoc($d);
        }
        return '';
    }

    private function deddoc($v): string
    {
        $k = (int) $v;
        return isset(self::DEDDOC[$k]) ? self::DEDDOC[$k] : '';
    }

    private function fechaIso($value): string
    {
        if ($value === null || $value === '' || strpos((string) $value, '0000-00-00') === 0) {
            return '';
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        return substr((string) $value, 0, 10);
    }

    private function fechaCorta($value): string
    {
        $iso = $this->fechaIso($value);
        if ($iso === '') {
            return '';
        }
        $ts = strtotime($iso);
        return $ts === false ? '' : date('j/n/Y', $ts);
    }

    private function corta($v, int $n): string
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1) . '.' : $v;
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
