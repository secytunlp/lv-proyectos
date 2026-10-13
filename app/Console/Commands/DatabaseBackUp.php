<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Backup diario de la BD (mismo esquema que lv-torneo).
 *
 * - mysqldump a .sql y compresión a .sql.gz por bloques (no carga el dump en memoria).
 * - El .sql.gz queda en storage/app/backup y se borran los de más de backup.keep_days días.
 * - Se adjunta al mail sólo si pesa hasta backup.max_attach_mb; si no, aviso sin adjunto
 *   (el mailer arma el mensaje en memoria y Gmail rechaza adjuntos grandes).
 * - Las credenciales van a mysqldump por un archivo temporal 0600 (--defaults-extra-file),
 *   no por línea de comandos, para que no queden visibles en `ps`.
 *
 * Lo dispara el crontab del servidor directamente (no hay schedule:run), p. ej.:
 *   0 4 * * * /usr/bin/php /var/www/sicadi/artisan database:backup >> /dev/null 2>&1
 */
class DatabaseBackUp extends Command
{
    protected $signature = 'database:backup';

    protected $description = 'Backup diario de la BD (mysqldump .sql.gz + aviso por mail)';

    public function handle()
    {
        $dir = storage_path('app/backup');
        if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
            $this->error('No se pudo crear ' . $dir);
            return 1;
        }

        $app = config('app.name', 'lv-proyectos');
        $fecha = Carbon::now()->format('Y-m-d');
        $sqlPath = $dir . '/backup-' . $fecha . '.sql';
        $gzPath = $sqlPath . '.gz';

        $keepDays = max(1, (int) config('backup.keep_days', 7));
        $maxAttachMb = (float) config('backup.max_attach_mb', 15);

        try {
            $this->dump($sqlPath);
            $this->comprimir($sqlPath, $gzPath);
        } catch (\Throwable $e) {
            if (is_file($gzPath)) {
                unlink($gzPath);
            }
            Log::error('database:backup falló: ' . $e->getMessage());
            $this->error($e->getMessage());
            $this->notificar(
                'ERROR BackUp ' . $app,
                'El backup de la BBDD ' . e($app) . ' del ' . $fecha . ' falló:<br><br><pre>' . e($e->getMessage()) . '</pre>'
            );
            return 1;
        } finally {
            if (is_file($sqlPath)) {
                unlink($sqlPath);
            }
        }

        $borrados = $this->rotar($dir, $keepDays, basename($gzPath));

        $bytes = filesize($gzPath);
        $mb = round($bytes / 1048576, 1);
        $adjuntar = $bytes <= $maxAttachMb * 1048576;

        $body = 'Backup de la BBDD ' . e($app) . ' realizado el ' . $fecha . ' (' . $mb . ' MB comprimido).<br><br>';
        if ($adjuntar) {
            $body .= 'Va adjunto. ';
        } else {
            $body .= 'Supera el límite de ' . $maxAttachMb . ' MB para adjuntar, así que no va en el mail. ';
        }
        $body .= 'Queda guardado en el servidor en storage/app/backup/' . basename($gzPath)
            . ' (se conservan ' . $keepDays . ' días).';
        if ($borrados > 0) {
            $body .= '<br>Se borraron ' . $borrados . ' backup(s) viejo(s).';
        }

        $this->notificar('BackUp ' . $app, $body, $adjuntar ? [$gzPath] : []);
        $this->info('Backup OK: ' . $gzPath . ' (' . $mb . ' MB)');

        return 0;
    }

    private function dump($sqlPath)
    {
        $db = config('database.connections.mysql');

        // Credenciales en un .cnf temporal legible sólo por el usuario del cron.
        $cnf = tempnam(sys_get_temp_dir(), 'bkcnf');
        if ($cnf === false) {
            throw new \RuntimeException('No se pudo crear el archivo temporal de credenciales.');
        }
        chmod($cnf, 0600);
        $esc = function ($v) {
            return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], (string) $v) . '"';
        };
        file_put_contents($cnf, "[client]\n"
            . 'user=' . $esc($db['username']) . "\n"
            . 'password=' . $esc($db['password']) . "\n"
            . 'host=' . $esc($db['host']) . "\n"
            . 'port=' . (int) ($db['port'] ?: 3306) . "\n");

        $command = escapeshellcmd(config('backup.dump_path', 'mysqldump'))
            . ' --defaults-extra-file=' . escapeshellarg($cnf)
            . ' --single-transaction --quick'
            . ' --result-file=' . escapeshellarg($sqlPath)
            . ' ' . escapeshellarg($db['database'])
            . ' 2>&1';

        $output = [];
        $returnVar = null;
        try {
            exec($command, $output, $returnVar);
        } finally {
            @unlink($cnf);
        }

        if ($returnVar !== 0) {
            throw new \RuntimeException('mysqldump terminó con código ' . $returnVar . ': ' . implode("\n", $output));
        }
        if (!is_file($sqlPath) || filesize($sqlPath) === 0) {
            throw new \RuntimeException('mysqldump no generó el archivo o quedó vacío. ' . implode("\n", $output));
        }
    }

    private function comprimir($origen, $destino)
    {
        $in = fopen($origen, 'rb');
        if ($in === false) {
            throw new \RuntimeException('No se pudo abrir ' . $origen);
        }
        $out = gzopen($destino, 'wb6');
        if ($out === false) {
            fclose($in);
            throw new \RuntimeException('No se pudo crear ' . $destino);
        }

        try {
            while (!feof($in)) {
                $chunk = fread($in, 1048576);
                if ($chunk === false) {
                    throw new \RuntimeException('Error leyendo ' . $origen);
                }
                if ($chunk !== '' && gzwrite($out, $chunk) === false) {
                    throw new \RuntimeException('Error escribiendo ' . $destino . ' (¿disco lleno?)');
                }
            }
        } finally {
            fclose($in);
            gzclose($out);
        }
    }

    /**
     * Borra backup-*.sql.gz / .sql con más de $keepDays días. Devuelve cuántos borró.
     */
    private function rotar($dir, $keepDays, $actual)
    {
        $limite = time() - $keepDays * 86400;
        $borrados = 0;

        foreach (glob($dir . '/backup-*') ?: [] as $archivo) {
            if (basename($archivo) === $actual || !is_file($archivo)) {
                continue;
            }
            if (!preg_match('/\.(sql\.gz|sql)$/', $archivo)) {
                continue;
            }
            if (filemtime($archivo) < $limite && @unlink($archivo)) {
                $borrados++;
            }
        }

        return $borrados;
    }

    private function notificar($subject, $body, array $attachs = [])
    {
        try {
            Mail::html($body, function ($message) use ($subject, $attachs) {
                $message->to(config('backup.destino'))->subject($subject);
                foreach ($attachs as $path) {
                    $message->attach($path);
                }
            });
        } catch (\Throwable $e) {
            Log::error('database:backup: no se pudo enviar el mail: ' . $e->getMessage());
            $this->warn('No se pudo enviar el mail de aviso (ver laravel.log).');
        }
    }
}
