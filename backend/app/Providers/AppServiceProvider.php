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
        // AudioSynthesizerInterfaceのバインド設定
        $this->app->bind(
            \App\Contracts\AudioSynthesizerInterface::class,
            function ($app) {
                // 環境に応じて異なる実装を注入
                if ($app->environment('testing')) {
                    // テスト環境: モック実装
                    return new \App\Services\MockAudioSynthesizer();
                }
                
                if ($app->environment('local') && config('app.debug') && config('audio.use_mock', false)) {
                    // 開発環境: モック使用が設定されている場合
                    return new \App\Services\MockAudioSynthesizer();
                }
                
                // 本番・開発環境: 実際のPollyService
                return new \App\Services\PollyService();
            }
        );
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
