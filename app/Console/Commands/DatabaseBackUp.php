<?php

namespace App\Console\Commands;

use App\Constants;
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
 * - Si backup.rclone_remote está configurado, sube el .sql.gz ahí (p. ej. Google Drive)
 *   y borra en el remoto los de más de backup.remote_keep_days días. Si la subida falla,
 *   el backup local queda igual y el mail lo avisa.
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
        $remoto = $this->subirRemoto($gzPath);

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
        if ($remoto !== null) {
            $body .= '<br><br>' . $remoto['mensaje'];
        }

        $subject = 'BackUp ' . $app;
        if ($remoto !== null && !$remoto['ok']) {
            $subject = 'ERROR subida BackUp ' . $app;
        }

        $this->notificar($subject, $body, $adjuntar ? [$gzPath] : []);
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

    /**
     * Sube el backup al remoto de rclone y rota los viejos allá.
     * Devuelve null si no hay remoto configurado, o ['ok' => bool, 'mensaje' => html].
     */
    private function subirRemoto($gzPath)
    {
        $remote = rtrim((string) config('backup.rclone_remote', ''), '/');
        if ($remote === '') {
            return null;
        }
        $rclone = escapeshellcmd(config('backup.rclone_path') ?: 'rclone');
        $destino = $remote . '/' . basename($gzPath);

        $output = [];
        $code = null;
        exec($rclone . ' copyto ' . escapeshellarg($gzPath) . ' ' . escapeshellarg($destino)
            . ' --retries 3 --low-level-retries 10 2>&1', $output, $code);

        if ($code !== 0) {
            $err = implode("\n", array_slice($output, -15));
            Log::error('database:backup: falló la subida a ' . $destino . ': ' . $err);
            $this->warn('Falló la subida a ' . $destino);
            return [
                'ok' => false,
                'mensaje' => '<b>No se pudo subir a ' . e($destino) . '</b> (rclone código ' . $code . '):<br><pre>' . e($err) . '</pre>',
            ];
        }

        $this->info('Subido a ' . $destino);
        $mensaje = 'Copia subida a ' . e($destino) . '.';

        $remoteKeep = (int) config('backup.remote_keep_days', 30);
        if ($remoteKeep > 0) {
            $out2 = [];
            $code2 = null;
            // --drive-use-trash=false: si no, en Drive lo borrado sigue ocupando espacio en la papelera.
            exec($rclone . ' delete ' . escapeshellarg($remote)
                . ' --min-age ' . $remoteKeep . 'd --include ' . escapeshellarg('backup-*.sql.gz')
                . ' --drive-use-trash=false 2>&1', $out2, $code2);
            if ($code2 !== 0) {
                Log::warning('database:backup: no se pudieron borrar los viejos en ' . $remote . ': ' . implode("\n", $out2));
                $mensaje .= ' (No se pudieron borrar los de más de ' . $remoteKeep . ' días allá; ver laravel.log.)';
            } else {
                $mensaje .= ' Allá se conservan ' . $remoteKeep . ' días.';
            }
        }

        return ['ok' => true, 'mensaje' => $mensaje];
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
        // Default acá también: si el servidor tiene config:cache viejo, config/backup.php
        // no está en la caché y config('backup.*') devuelve null.
        $destino = config('backup.destino') ?: 'marcosp@presi.unlp.edu.ar';

        try {
            Mail::html($body, function ($message) use ($subject, $attachs, $destino) {
                // Mismo remitente que usa el sistema en sus mails (SicadiEnviada & cía.):
                // el SMTP no acepta el MAIL_FROM_ADDRESS por defecto de config/mail.php.
                $message->from(Constants::MAIL_SICADI, Constants::NOMBRE_SICADI)
                    ->to($destino)
                    ->subject($subject);
                foreach ($attachs as $path) {
                    $message->attach($path);
                }
            });
        } catch (\Throwable $e) {
            Log::error('database:backup: no se pudo enviar el mail: ' . $e->getMessage());
            $this->warn('No se pudo enviar el mail de aviso: ' . $e->getMessage());
        }
    }
}
