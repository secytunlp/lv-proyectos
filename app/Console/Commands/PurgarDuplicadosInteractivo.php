<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Purga interactiva de investigadores duplicados.
 *
 * Para CADA grupo ALTA:
 * 1. Muestra datos de ambos investigadores (CUIL, proyecto, etc)
 * 2. Te pregunta cuál CUIL mantener
 * 3. Ejecuta la fusión
 *
 * Uso: php artisan investigadores:purgar-interactivo
 */
class PurgarDuplicadosInteractivo extends Command
{
    protected $signature = 'investigadores:purgar-interactivo
        {--solo-alta : Procesar solo ALTA (default)}
        {--saltear-n= : Saltear primeros N grupos (para retomar)}';

    protected $description = 'Purga interactiva: revisa cada grupo y decides el CUIL a mantener.';

    public function handle()
    {
        $solo_alta = true;
        $saltear_n = (int) ($this->option('saltear-n') ?? 0);

        $this->info("=== PURGA INTERACTIVA DE DUPLICADOS ===");
        $this->line("");

        // --- Detectar duplicados ---
        $filas = DB::select(
            'SELECT i.id, p.cuil, p.apellido, p.nombre, p.nacimiento, '.
            '       i.facultad_id, ca.nombre AS categoria, si.nombre AS sicadi '.
            'FROM investigadors i '.
            'JOIN personas p ON p.id = i.persona_id '.
            'LEFT JOIN categorias ca ON ca.id = i.categoria_id '.
            'LEFT JOIN sicadis   si ON si.id = i.sicadi_id '.
            'ORDER BY i.id'
        );

        $this->info('Analizando '.count($filas).' investigadores...');

        // Actividad
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

        // Agrupar
        $grupos_alta = $this->detectarDuplicados($filas, $act);

        $this->line('');
        $this->info('Grupos ALTA detectados: '.count($grupos_alta));
        $this->line('');

        if (count($grupos_alta) === 0) {
            $this->info('Sin duplicados para revisar.');
            return 0;
        }

        // --- Procesar cada grupo ---
        $timestamp = date('YmdHis');
        $tabla_backup = "investigadors_backup_interactivo_$timestamp";

        // Backup global
        $this->line('📦 Backup global de investigadors...');
        DB::statement("CREATE TABLE $tabla_backup LIKE investigadors");
        DB::statement("INSERT INTO $tabla_backup SELECT * FROM investigadors");
        $this->line("   ✓ $tabla_backup");
        $this->line('');

        $procesados = 0;
        $saltados = 0;

        foreach ($grupos_alta as $idx => $grupo) {
            if ($idx < $saltear_n) {
                $saltados++;
                continue;
            }

            $ids = $grupo['ids'];
            $this->line('');
            $this->line("═══════════════════════════════════════════════════════════════");
            $this->line("Grupo ".($idx - $saltear_n + 1)." de ".(count($grupos_alta) - $saltear_n).": {$grupo['apellido']}, {$grupo['nombre']}");
            $this->line("Motivo: {$grupo['motivo']}");
            $this->line("═══════════════════════════════════════════════════════════════");

            // Mostrar datos de cada investigador
            foreach ($ids as $pos => $id) {
                $inv = DB::table('investigadors')->where('id', $id)->first();
                $pers = DB::table('personas')->where('id', $inv->persona_id)->first();
                $cat = $inv->categoria_id ? DB::table('categorias')->where('id', $inv->categoria_id)->first()->nombre : '-';
                $sic = $inv->sicadi_id ? DB::table('sicadis')->where('id', $inv->sicadi_id)->first()->nombre : '-';
                $n_integrantes = DB::table('integrantes')->where('investigador_id', $id)->count();
                $n_becas = DB::table('investigador_becas')->where('investigador_id', $id)->count();

                $label = $pos === 0 ? '🔴 OPCIÓN 1 (menor)' : '🔵 OPCIÓN 2 (mayor)';

                $this->line('');
                $this->line("$label:");
                $this->line("  Investigador ID: $id");
                $this->line("  CUIL: {$pers->cuil}");
                $this->line("  Nombre: {$pers->apellido}, {$pers->nombre}");
                $this->line("  Nacimiento: {$pers->nacimiento}");
                $this->line("  Categoría: $cat");
                $this->line("  SICADI: $sic");
                $this->line("  Integrantes: $n_integrantes");
                $this->line("  Becas: $n_becas");

                if (!empty($pers->email)) {
                    $this->line("  Email: {$pers->email}");
                }
                if (!empty($pers->telefono)) {
                    $this->line("  Teléfono: {$pers->telefono}");
                }

                // Ver si hay usuario
                $user = DB::table('users')->where('cuil', $pers->cuil)->first();
                if ($user) {
                    $this->line("  👤 Usuario: {$user->email} (ID {$user->id})");
                }
            }

            $this->line('');

            // Decisión del usuario
            $choice = $this->choice(
                '¿Cuál CUIL mantener?',
                [
                    "1: {$ids[0]} - CUIL {$this->getCuil($ids[0])} (ID menor)",
                    "2: {$ids[1]} - CUIL {$this->getCuil($ids[1])} (ID mayor)",
                    "Saltear este grupo",
                    "Cancelar purga",
                ]
            );

            if (strpos($choice, 'Cancelar') !== false) {
                $this->warn('Purga cancelada.');
                return 1;
            }

            if (strpos($choice, 'Saltear') !== false) {
                $this->line('✏️  Salteado (retoma con --saltear-n='.($idx+1).')');
                continue;
            }

            // Decidir qué ID mantener y cuál eliminar
            if (strpos($choice, 'ID menor') !== false) {
                $id_keep = $ids[0];
                $id_remove = $ids[1];
            } else {
                $id_keep = $ids[1];
                $id_remove = $ids[0];
            }

            // Ejecutar fusión
            try {
                DB::transaction(function () use ($id_keep, $id_remove) {
                    $this->fusionarPar($id_keep, $id_remove);
                });

                $this->info("✅ Fusión completada: mantiene ID $id_keep, elimina ID $id_remove");
                $procesados++;

            } catch (\Exception $e) {
                $this->error("❌ Error en fusión: {$e->getMessage()}");

                $continuar = $this->confirm('¿Continuar con el siguiente grupo?', true);
                if (!$continuar) {
                    return 1;
                }
            }
        }

        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->info('✅ PURGA COMPLETADA');
        $this->line("   Fusiones: $procesados");
        if ($saltados > 0) {
            $this->line("   Salteados: $saltados");
        }
        $this->line("   Backup: $tabla_backup");
        $this->line('');

        return 0;
    }

    private function getCuil($id)
    {
        $inv = DB::table('investigadors')->where('id', $id)->first();
        if (!$inv) return '?';
        $pers = DB::table('personas')->where('id', $inv->persona_id)->first();
        return $pers ? $pers->cuil : '?';
    }

    private function fusionarPar($id_keep, $id_remove)
    {
        $inv_keep = DB::table('investigadors')->where('id', $id_keep)->first();
        $inv_remove = DB::table('investigadors')->where('id', $id_remove)->first();

        if (!$inv_keep || !$inv_remove) {
            throw new \Exception("Investigador no encontrado");
        }

        $persona_keep = DB::table('personas')->where('id', $inv_keep->persona_id)->first();
        $persona_remove = DB::table('personas')->where('id', $inv_remove->persona_id)->first();

        if (!$persona_keep || !$persona_remove) {
            throw new \Exception("Persona no encontrada");
        }

        // 1. Mover TODOS los integrantes
        DB::table('integrantes')
            ->where('investigador_id', $id_remove)
            ->update(['investigador_id' => $id_keep]);

        // 2. Mover TODAS las becas
        DB::table('investigador_becas')
            ->where('investigador_id', $id_remove)
            ->update(['investigador_id' => $id_keep]);

        // 3. Mover relaciones en pivots
        $pivots = [
            'investigador_titulos', 'investigador_tituloposts', 'investigador_cargos',
            'investigador_categorias', 'investigador_carreras', 'investigador_sicadis',
        ];
        foreach ($pivots as $pivot) {
            DB::table($pivot)
                ->where('investigador_id', $id_remove)
                ->update(['investigador_id' => $id_keep]);
        }

        // 4. Copiar datos OPCIONALES de persona_remove a persona_keep (no documento)
        $campos_fusion = [
            'email', 'telefono', 'calle', 'nro', 'piso', 'depto',
            'localidad', 'cp', 'observaciones', 'genero', 'foto', 'fallecimiento'
        ];
        $update_data = [];
        foreach ($campos_fusion as $campo) {
            $valor_keep = $persona_keep->{$campo} ?? null;
            $valor_remove = $persona_remove->{$campo} ?? null;
            if (($valor_keep === null || $valor_keep === '') && $valor_remove !== null && $valor_remove !== '') {
                $update_data[$campo] = $valor_remove;
            }
        }
        if (!empty($update_data)) {
            DB::table('personas')
                ->where('id', $persona_keep->id)
                ->update($update_data);
        }

        // 5. Eliminar usuario del investigador removido
        $user_remove = DB::table('users')
            ->where('cuil', $persona_remove->cuil)
            ->first();
        if ($user_remove) {
            DB::table('users')->where('id', $user_remove->id)->delete();
        }

        // 6. Eliminar investigador redundante
        DB::table('investigadors')->where('id', $id_remove)->delete();

        // 7. Eliminar persona redundante
        DB::table('personas')->where('id', $persona_remove->id)->delete();
    }

    private function detectarDuplicados($filas, $act)
    {
        $grupos = [];
        $porDoc = [];
        $porNombre = [];

        foreach ($filas as $f) {
            $f->_doc = $this->doc($f->cuil);
            $f->_nom = $this->nom($f->apellido, $f->nombre);
            $f->_huella = $this->huella($f->_doc);
            $f->_nac = ($f->nacimiento === null || $f->nacimiento === '')
                        ? '' : substr((string) $f->nacimiento, 0, 10);

            $porNombre[$f->_nom][] = $f;
            if ($f->_doc !== '') {
                $porDoc[$f->_doc][] = $f;
            }
        }

        $vistos = [];

        // Duplicados por documento
        foreach ($porDoc as $doc => $miembros) {
            if (count($miembros) < 2) continue;
            $ids = array_map(fn($m) => (int)$m->id, $miembros);
            sort($ids);
            $ids_key = implode(',', $ids);
            if (isset($vistos[$ids_key])) continue;
            $vistos[$ids_key] = true;
            $grupos[] = $this->armarGrupo($miembros, 'ALTA', "mismo documento $doc");
        }

        // Duplicados por nombre+nacimiento
        foreach ($porNombre as $nom => $miembros) {
            if (count($miembros) < 2) continue;

            $porNac = [];
            foreach ($miembros as $m) {
                if ($m->_nac !== '') {
                    $porNac[$m->_nac][] = $m;
                }
            }

            foreach ($porNac as $nac => $grupo) {
                if (count($grupo) < 2) continue;
                $ids = array_map(fn($m) => (int)$m->id, $grupo);
                sort($ids);
                $ids_key = implode(',', $ids);
                if (isset($vistos[$ids_key])) continue;
                $vistos[$ids_key] = true;
                $grupos[] = $this->armarGrupo($grupo, 'ALTA', "mismo nombre+nacimiento $nac");
            }
        }

        return $grupos;
    }

    private function armarGrupo($miembros, $conf, $motivo)
    {
        $ids = [];
        foreach ($miembros as $m) {
            $ids[] = (int) $m->id;
        }
        sort($ids);

        $p = $miembros[0];
        return [
            'conf' => $conf,
            'apellido' => $p->apellido,
            'nombre' => $p->nombre,
            'ids' => $ids,
            'motivo' => $motivo,
        ];
    }

    private function doc($cuil)
    {
        $d = preg_replace('/\D/', '', (string) $cuil);
        if (strlen($d) < 9) return '';
        return substr(substr($d, 0, -1), -8);
    }

    private function nom($apellido, $nombre)
    {
        $s = strtoupper(trim($apellido).' '.trim($nombre));
        $s = strtr($s, [
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','Ñ'=>'N',
            'á'=>'A','é'=>'E','í'=>'I','ó'=>'O','ú'=>'U','ü'=>'U','ñ'=>'N',
        ]);
        return preg_replace('/\s+/', ' ', $s);
    }

    private function huella($doc)
    {
        if ($doc === '') return '';
        $a = str_split($doc);
        sort($a);
        return implode('', $a);
    }
}
