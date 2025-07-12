<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TravelSpot;
use App\Services\GooglePlacesService;

class UpdatePlaceIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'travel-spots:update-place-ids {--force : 空のplace_idのみでなく、全ての場所を更新}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google Places APIを使用してtravel_spotsのplace_idを自動更新';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Place IDの更新を開始します...');
        
        try {
            $placesService = new GooglePlacesService();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
        
        // 更新対象のスポットを取得
        $query = TravelSpot::query();
        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('place_id')->orWhere('place_id', '');
            });
        }
        
        $spots = $query->get();
        
        if ($spots->isEmpty()) {
            $this->info('更新対象のスポットがありません。');
            return;
        }
        
        $this->info("更新対象: {$spots->count()}件");
        
        $progressBar = $this->output->createProgressBar($spots->count());
        $progressBar->start();
        
        $updated = 0;
        
        foreach ($spots as $spot) {
            $placeId = $placesService->getPlaceId($spot->name, $spot->prefecture);
            
            if ($placeId) {
                $spot->update(['place_id' => $placeId]);
                $updated++;
                $this->line("\n✓ {$spot->name}: {$placeId}");
            } else {
                $this->line("\n✗ {$spot->name}: Place ID not found");
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        
        $this->newLine(2);
        $this->info("更新完了: {$updated}件のPlace IDを更新しました。");
    }
}
