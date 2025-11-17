<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
        public function boot()
        {
            Schema::defaultStringLength(255);
            /*DB::listen(function ($query) {
                Log::debug("DB: " . $query->sql . "[".  implode(",",$query->bindings). "]");
            });*/
            if ($this->app->environment('production')) {
                URL::forceScheme('https');
            }

        }
}
