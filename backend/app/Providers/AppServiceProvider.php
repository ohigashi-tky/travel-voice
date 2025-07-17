<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\AudioGuideQualityCheck;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 本番環境でHTTPS強制
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                AudioGuideQualityCheck::class,
            ]);
        }
        
    }
}
