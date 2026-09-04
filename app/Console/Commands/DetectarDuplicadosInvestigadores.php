<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Detecta investigadores duplicados: la MISMA persona cargada dos o mas veces
 * en `investigadors`, cada vez con un CUIL distinto.
 *
 * No es un problema de categorias sino de identidad, y por eso ninguno de los
 * controles de categorias lo ve: para ellos el 92574 y el 101688 son dos
 * investigadores distintos que casualmente difieren. Henao aparecia roto en
 * tres controles a la vez sin que ninguno explicara por que.
 *
 * Ninguna señal sola alcanza:
 *   - por documento: precision altisima, cobertura minima (los duplicados de
 *     extranjeros tienen dos documentos distintos, no hay numero en comun).
 *   - por nombre: encuentra todo pero mete homonimos (tres GOMEZ JUAN CARLOS
 *     de tres generaciones no son un duplicado).
 *   - por nombre + nacimiento: casi concluyente, pero al 2026-09 el 20% de
 *     `personas` no tiene nacimiento cargado, y son justo los registros viejos.
 *
 * Por eso se combinan y cada grupo sale con un nivel de confianza:
 *
 *   ALTA   mismo documento normalizado; o mismo nombre + mismo nacimiento;
 *          o mismo nombre + los mismos digitos en el documento (transpuestos)
 *   MEDIA  mismo nombre + uno de los CUIL es provisorio de ANSES (bloque 9x/6x)
 *          o mismo nombre + misma facultad y la categoria esta en un solo lado
 *   BAJA   solo coincide el nombre: probable homonimo, no se lista por defecto
 *
 * Ademas cruza la ACTIVIDAD de cada registro (integrantes: cuantos proyectos y
 * entre que fechas). Si dos registros del grupo estuvieron activos AL MISMO
 * TIEMPO en proyectos distintos, es evidencia de que son dos personas: nadie
 * figura dos veces en paralelo. Eso degrada MEDIA/BAJA a homonimo.
 * En los ALTA no degrada: ahi el solapamiento solo significa que el duplicado
 * se uso de los dos lados a la vez, que es justamente lo que pasa cuando una
 * persona quedo cargada dos veces.
 *
 * Solo lee: no modifica nada.
 */
class DetectarDuplicadosInvestigadores extends Command
{
    protected $signature = 'investigadores:duplicados
        {--solo= : Filtrar por confianza (ALTA, MEDIA, BAJA)}
        {--incluir-baja : Listar tambien los BAJA (probables homonimos)}
        {--limite=0 : Cortar el listado en N grupos (0 = sin limite)}';

    protected $description = 'Detecta la misma persona cargada dos veces en investigadors, con nivel de confianza';

    private $actividad = array();

    /** true si dos registros del grupo estuvieron activos en años que se pisan */
    private function solapan($miembros)
    {
        $iv = array();
        foreach ($miembros as $m) {
            $id = (int) $m->id;
            if (isset($this->actividad[$id]) && $this->actividad[$id]['mn'] !== '') {
                $iv[] = array((int) $this->actividad[$id]['mn'], (int) $this->actividad[$id]['mx']);
            }
        }
        for ($i = 0; $i < count($iv); $i++) {
            for ($j = $i + 1; $j < count($iv); $j++) {
                if ($iv[$i][0] <= $iv[$j][1] && $iv[$j][0] <= $iv[$i][1]) {
                    return true;
                }
            }
        }
        return false;
    }

    /** Deja solo los digitos del documento: saca el verificador y toma los ultimos 8 */
    private function doc($cuil)
    {
        $d = preg_replace('/\D/', '', (string) $cuil);
        if (strlen($d) < 9) {
            return '';
        }
        return substr(substr($d, 0, -1), -8);
    }

    /** Nombre comparable: mayusculas, sin tildes, sin espacios de mas */
    private function nom($apellido, $nombre)
    {
        $s = strtoupper(trim($apellido).' '.trim($nombre));
        $s = strtr($s, array(
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N',
            'á'=>'A','é'=>'E','í'=>'I','ó'=>'O','ú'=>'U','ü'=>'U','ñ'=>'N',
        ));
        return preg_replace('/\s+/', ' ', $s);
    }

    /** Digitos ordenados: dos documentos con los mismos digitos transpuestos coinciden */
    private function huella($doc)
    {
        if ($doc === '') {
            return '';
        }
        $a = str_split($doc);
        sort($a);
        return implode('', $a);
    }

    /** s/c y - no son un dato cargado: cuentan como sin categoria */
    private function tieneDato($v)
    {
        if ($v === null) {
            return false;
        }
        return !in_array(strtoupper(trim($v)), array('S/C', 'SC', '-', ''), true);
    }

    /** Documento de extranjero provisorio de ANSES */
    private function provisorio($doc)
    {
        return $doc !== '' && strlen($doc) === 8 && ($doc[0] === '9' || $doc[0] === '6');
    }

    public function handle()
    {
        $solo         = $this->option('solo');
        $incluirBaja  = (bool) $this->option('incluir-baja');
        $limite       = (int) $this->option('limite');

        $filas = DB::select(
            'SELECT i.id, p.cuil, p.apellido, p.nombre, p.nacimiento, '.
            '       i.facultad_id, ca.nombre AS categoria, si.nombre AS sicadi '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN categorias ca ON ca.id = i.categoria_id '.
            'LEFT JOIN sicadis   si ON si.id = i.sicadi_id '.
            'ORDER BY i.id'
        );

        $this->info('Revisando '.count($filas).' investigadores...');

        $act = array();
        foreach (DB::select(
            'SELECT investigador_id, COUNT(*) AS n, MIN(alta) AS mn, MAX(alta) AS mx '.
            'FROM integrantes WHERE investigador_id IS NOT NULL GROUP BY investigador_id'
        ) as $a) {
            $act[(int) $a->investigador_id] = array(
                'n'  => (int) $a->n,
                'mn' => $a->mn === null ? '' : substr((string) $a->mn, 0, 4),
                'mx' => $a->mx === null ? '' : substr((string) $a->mx, 0, 4),
            );
        }
        $this->actividad = $act;

        $porNombre = array();
        $porDoc    = array();

        foreach ($filas as $f) {
            $f->_doc     = $this->doc($f->cuil);
            $f->_nom     = $this->nom($f->apellido, $f->nombre);
            $f->_huella  = $this->huella($f->_doc);
            $f->_nac     = ($f->nacimiento === null || $f->nacimiento === '')
                            ? '' : substr((string) $f->nacimiento, 0, 10);

            $porNombre[$f->_nom][] = $f;
            if ($f->_doc !== '') {
                $porDoc[$f->_doc][] = $f;
            }
        }

        $grupos = array();
        $vistos = array();

        // --- ALTA por documento: no necesita que coincida el nombre ---
        foreach ($porDoc as $doc => $miembros) {
            if (count($miembros) < 2) {
                continue;
            }
            $ids = $this->ids($miembros);
            $vistos[$ids] = true;
            $grupos[] = $this->grupo($miembros, 'ALTA', 'mismo documento '.$doc);
        }

        // --- Grupos por nombre ---
        // --- Grupos por nombre ---
        $this->pasada($porNombre, '', $grupos, $vistos);

        // --- Segunda pasada: el nombre puede estar invertido ---
        // "DIAZ, MARIA JULIETA" y "DIAZ, JULIETA MARIA" dan la misma clave si se
        // ordenan las palabras. Sin esto, un duplicado con el nombre dado vuelta
        // en uno de los dos registros no se agrupa nunca. Caso real: en SIGEVA el
        // DNI 28671709 es DIAZ, JULIETA MARIA y en produccion figura al reves.
        $porOrden = array();
        foreach ($filas as $f) {
            $porOrden[$this->nomOrdenado($f->apellido, $f->nombre)][] = $f;
        }
        $this->pasada($porOrden, ' (nombre en distinto orden)', $grupos, $vistos);

        // ------------------------------------------------------------------
        $orden = array('ALTA' => 0, 'MEDIA' => 1, 'BAJA' => 2);
        usort($grupos, function ($a, $b) use ($orden) {
            if ($orden[$a['conf']] !== $orden[$b['conf']]) {
                return $orden[$a['conf']] - $orden[$b['conf']];
            }
            return strcmp($a['persona'], $b['persona']);
        });

        $resumen = array('ALTA' => 0, 'MEDIA' => 0, 'BAJA' => 0);
        $conDato = 0;
        $rows    = array();

        foreach ($grupos as $g) {
            $resumen[$g['conf']]++;
            if ($g['dato_partido']) {
                $conDato++;
            }
            if ($g['conf'] === 'BAJA' && !$incluirBaja) {
                continue;
            }
            if ($solo !== null && $solo !== '' && stripos($g['conf'], $solo) === false) {
                continue;
            }
            $rows[] = array(
                $g['conf'],
                $g['persona'],
                $g['ids'],
                $g['cuils'],
                $g['nacs'],
                $g['cats'],
                $g['sics'],
                $g['acts'],
                $g['motivo'],
            );
        }

        if (count($grupos) === 0) {
            $this->info('Sin duplicados detectados.');
            return 0;
        }

        $recortado = false;
        if ($limite > 0 && count($rows) > $limite) {
            $rows = array_slice($rows, 0, $limite);
            $recortado = true;
        }

        if (count($rows) > 0) {
            $this->table(
                array('Conf.', 'Persona', 'Ids', 'CUILs', 'Nacimiento', 'Categorias', 'SICADIs', 'Actividad', 'Motivo'),
                $rows
            );
            if ($recortado) {
                $this->warn('Listado recortado a '.$limite.' grupos (usa --limite=0 para verlos todos).');
            }
        }

        $this->line('');
        $this->info('Grupos detectados:');
        $this->table(
            array('Confianza', 'Grupos'),
            array(
                array('ALTA',  $resumen['ALTA']),
                array('MEDIA', $resumen['MEDIA']),
                array('BAJA (probable homonimo)', $resumen['BAJA']),
            )
        );
        $this->line('Total: '.count($grupos));
        $this->warn($conDato.' grupos tienen la categoria o el SICADI cargados en un solo lado:');
        $this->line('en esos, fusionar no es cosmetico, cambia el dato de la persona.');

        if (!$incluirBaja && $resumen['BAJA'] > 0) {
            $this->line('');
            $this->line('Los '.$resumen['BAJA'].' BAJA no se listan (solo coincide el nombre). --incluir-baja para verlos.');
        }

        $this->line('');
        $this->line('ALTA se puede revisar de a lotes; MEDIA y BAJA necesitan ojo humano.');
        $this->line('Al fusionar, acordarse de users.cuil: es UNIQUE y decide el acceso a los tramites.');

        return 0;
    }


    /** Igual que nom() pero con las palabras ordenadas: detecta nombres invertidos */
    private function nomOrdenado($apellido, $nombre)
    {
        $t = explode(' ', $this->nom($apellido, $nombre));
        sort($t);
        return implode(' ', $t);
    }

    /** Recorre un indice nombre => registros y arma los grupos que falten */
    private function pasada($indice, $sufijo, &$grupos, &$vistos)
    {
        foreach ($indice as $clave => $miembros) {
            if (count($miembros) < 2) {
                continue;
            }
            foreach ($porNombre as $nom => $miembros) {
                if (count($miembros) < 2) {
                    continue;
                }

                // Subagrupar por nacimiento cuando lo hay: separa homonimos reales
                $porNac = array();
                $sinNac = array();
                foreach ($miembros as $m) {
                    if ($m->_nac !== '') {
                        $porNac[$m->_nac][] = $m;
                    } else {
                        $sinNac[] = $m;
                    }
                }

                foreach ($porNac as $nac => $grupo) {
                    if (count($grupo) < 2) {
                        continue;
                    }
                    $ids = $this->ids($grupo);
                    if (isset($vistos[$ids])) {
                        continue;
                    }
                    $vistos[$ids] = true;
                    $grupos[] = $this->grupo($grupo, 'ALTA', 'mismo nombre y nacimiento '.$nac.$sufijo);
                }

                // Los que no se pudieron separar por nacimiento
                $resto = $sinNac;
                if (count($porNac) === 0) {
                    $resto = $miembros;
                }
                if (count($resto) < 2) {
                    continue;
                }

                $ids = $this->ids($resto);
                if (isset($vistos[$ids])) {
                    continue;
                }
                $vistos[$ids] = true;

                $huellas = array();
                $hayProv = false;
                $facs    = array();
                $conCat  = 0;
                foreach ($resto as $m) {
                    if ($m->_huella !== '') {
                        $huellas[$m->_huella] = true;
                    }
                    if ($this->provisorio($m->_doc)) {
                        $hayProv = true;
                    }
                    $facs[(string) $m->facultad_id] = true;
                    if ($this->tieneDato($m->categoria) || $this->tieneDato($m->sicadi)) {
                        $conCat++;
                    }
                }

                if (count($huellas) === 1 && count($resto) > 1) {
                    $conf = 'ALTA';
                    $motivo = 'mismo nombre y los mismos digitos en el documento'.$sufijo;
                } elseif ($hayProv) {
                    $conf = 'MEDIA';
                    $motivo = 'mismo nombre y un CUIL provisorio de ANSES'.$sufijo;
                } elseif (count($facs) === 1 && $conCat > 0 && $conCat < count($resto)) {
                    $conf = 'MEDIA';
                    $motivo = 'mismo nombre, misma facultad, categoria en un solo lado'.$sufijo;
                } else {
                    $conf = 'BAJA';
                    $motivo = 'solo coincide el nombre'.$sufijo;
                }

                if ($conf !== 'ALTA' && $this->solapan($resto)) {
                    $conf = 'BAJA';
                    $motivo = 'activos al mismo tiempo en proyectos distintos: son dos personas';
                }

                $grupos[] = $this->grupo($resto, $conf, $motivo);
            }
        }
    }

    private function ids($miembros)
    {
        $ids = array();
        foreach ($miembros as $m) {
            $ids[] = (int) $m->id;
        }
        sort($ids);
        return implode(',', $ids);
    }

    private function grupo($miembros, $conf, $motivo)
    {
        $ids = array(); $cuils = array(); $nacs = array(); $cats = array(); $sics = array(); $acts = array();
        $conCat = 0;
        foreach ($miembros as $m) {
            $ids[]   = (int) $m->id;
            $cuils[] = $m->cuil;
            $nacs[]  = $m->_nac === '' ? '-' : $m->_nac;
            $cats[]  = $m->categoria === null ? '-' : $m->categoria;
            $sics[]  = $m->sicadi === null ? '-' : $m->sicadi;
            $a = isset($this->actividad[(int) $m->id]) ? $this->actividad[(int) $m->id] : null;
            $acts[]  = $a === null ? 'sin proy.' : ($a['mn'].'-'.$a['mx'].' ('.$a['n'].')');
            if ($this->tieneDato($m->categoria) || $this->tieneDato($m->sicadi)) {
                $conCat++;
            }
        }
        $p = $miembros[0];
        return array(
            'conf'    => $conf,
            'persona' => $this->corta(trim($p->apellido.', '.$p->nombre), 28),
            'ids'     => implode(',', $ids),
            'cuils'   => implode(' | ', $cuils),
            'nacs'    => implode(' | ', array_unique($nacs)),
            'cats'    => implode(',', $cats),
            'sics'    => implode(',', $sics),
            'acts'    => implode(' | ', $acts),
            'motivo'  => $motivo,
            'dato_partido' => ($conCat > 0 && $conCat < count($miembros)),
        );
    }

    private function corta($v, $n)
    {
        $v = (string) $v;
        return mb_strlen($v) > $n ? mb_substr($v, 0, $n - 1).'.' : $v;
    }
}
