<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    public function handle($request, \Closure $next)
    {
        $ip = $request->ip();

        if (strpos($ip, '163.10.35.') === 0) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
