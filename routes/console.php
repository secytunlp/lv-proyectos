<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// El backup diario de la base (php artisan database:backup) NO se programa en el
// Kernel: lo dispara directo el crontab del servidor y no hay 'schedule:run':
//
//   0 4 * * * /usr/bin/php /var/www/sicadi/artisan database:backup >> /dev/null 2>&1
//
// Si algún día se pasa al scheduler de Laravel, mover esto a Kernel::schedule() y
// cambiar el crontab por 'schedule:run' cada minuto — no las dos cosas.
