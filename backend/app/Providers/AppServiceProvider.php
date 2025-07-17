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
        
        // Railway環境でのイベント取得自動実行
        if (config('app.env') === 'production' && env('RUN_EVENTS_FETCH', 'false') === 'true') {
            $this->app->booted(function () {
                if (!$this->app->runningInConsole() || $this->app->runningUnitTests()) {
                    return;
                }
                
                // バックグラウンドでイベント取得を実行
                register_shutdown_function(function () {
                    try {
                        Artisan::call('events:fetch');
                        \Log::info('イベント取得バッチが正常に完了しました');
                    } catch (\Exception $e) {
                        \Log::error('イベント取得バッチでエラーが発生しました: ' . $e->getMessage());
                    }
                });
            });
        }
    }
}
