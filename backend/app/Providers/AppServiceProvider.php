<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
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
        
        // CORS設定を確実に適用
        if (!$this->app->runningInConsole()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                AudioGuideQualityCheck::class,
            ]);
        }
    }
}
