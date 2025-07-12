<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TravelSpot;
use App\Models\Guide;

class GenerateAllGuides extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guides:generate-all {--force : 既存のガイドも上書きする}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '全ての観光地に対して詳細な音声ガイドを生成します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('全観光地の音声ガイドを生成中...');
        
        $force = $this->option('force');
        $spotsQuery = TravelSpot::query();
        
        if (!$force) {
            // 既存のガイドがない観光地のみ処理
            $spotsQuery->doesntHave('guides');
        }
        
        $spots = $spotsQuery->get();
        $this->info("処理対象: {$spots->count()}件の観光地");
        
        $progressBar = $this->output->createProgressBar($spots->count());
        $progressBar->start();
        
        $created = 0;
        $updated = 0;
        
        foreach ($spots as $spot) {
            try {
                $guideData = $this->generateGuideData($spot);
                
                if ($force && $spot->guides()->exists()) {
                    // 既存のガイドを更新
                    $spot->guides()->first()->update($guideData);
                    $updated++;
                } else {
                    // 新規ガイドを作成
                    Guide::create(array_merge($guideData, [
                        'tourist_spot_id' => $spot->id
                    ]));
                    $created++;
                }
                
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("\n{$spot->name}のガイド生成に失敗: {$e->getMessage()}");
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        $this->info("完了! 作成: {$created}件, 更新: {$updated}件");
    }
    
    /**
     * 観光地データから詳細なガイドデータを生成
     */
    private function generateGuideData(TravelSpot $spot): array
    {
        // カテゴリ別の説明テンプレート
        $categoryTemplates = [
            '神社・仏閣' => [
                'history_prefix' => 'この神聖な場所は',
                'cultural_aspect' => '信仰の場として多くの人々に愛され続けています',
                'highlights' => ['歴史的建造物', '文化的価値', '精神的意義']
            ],
            '城・城跡' => [
                'history_prefix' => 'この歴史ある城は',
                'cultural_aspect' => '日本の武士文化と建築技術の粋を集めた重要な文化遺産です',
                'highlights' => ['天守閣', '石垣', '歴史的価値', '眺望']
            ],
            '庭園・公園' => [
                'history_prefix' => 'この美しい庭園は',
                'cultural_aspect' => '日本の伝統的な造園技術と自然美が調和した空間です',
                'highlights' => ['四季の景観', '伝統的造園', '自然美', '散策路']
            ],
            '温泉' => [
                'history_prefix' => 'この温泉地は',
                'cultural_aspect' => '古くから湯治場として親しまれ、心身の癒しを提供してきました',
                'highlights' => ['良質な温泉', '癒しの効果', '自然環境', '温泉文化']
            ],
            'その他' => [
                'history_prefix' => 'この場所は',
                'cultural_aspect' => '地域の文化と歴史を象徴する重要な観光地です',
                'highlights' => ['歴史的価値', '文化的意義', '観光名所']
            ]
        ];
        
        $category = $spot->category ?? 'その他';
        $template = $categoryTemplates[$category] ?? $categoryTemplates['その他'];
        
        // 詳細な説明文を生成
        $detailedContent = "{$template['history_prefix']}、{$spot->description}";
        
        // 歴史的背景を追加
        if ($spot->history) {
            $detailedContent .= " {$spot->history}";
        } else {
            $detailedContent .= " 長い歴史を持つこの場所は、時代を超えて多くの人々に愛され続けています。";
        }
        
        // 文化的意義を追加
        $detailedContent .= " {$template['cultural_aspect']}";
        
        // 見どころを生成
        $highlights = $spot->highlights ?? $template['highlights'];
        
        return [
            'title' => "{$spot->name}完全ガイド",
            'content' => $detailedContent,
            'type' => 'audio',
            'duration_minutes' => 3,
            'order' => 1,
            'highlights' => $highlights,
            'is_active' => true,
        ];
    }
}