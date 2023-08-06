<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // app()->bind('classA', \App\Libs\ClassA::class);
        app()->bind('sendMessage', \App\Libs\SendMessage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 本番環境のみ、全てhttpsにする
        if (\App::environment(['production'])) {
            \URL::forceScheme('https');
        }
    }
}
