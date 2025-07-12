<?php

namespace App\Services;

use Aws\Polly\PollyClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\PronunciationCorrection;

class PollyService
{
    private PollyClient $pollyClient;
    private string $region;
    private string $outputFormat;
    private string $voiceId;
    private string $engine;

    public function __construct()
    {
        $this->region = config('services.aws.polly_region', 'ap-northeast-1');
        $this->outputFormat = config('services.aws.polly_output_format', 'mp3');
        $this->voiceId = config('services.aws.polly_voice_id', 'Takumi');
        $this->engine = config('services.aws.polly_engine', 'neural');

        $this->pollyClient = new PollyClient([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => config('services.aws.key'),
                'secret' => config('services.aws.secret'),
            ],
        ]);
    }

    /**
     * テキストを音声に変換
     *
     * @param string $text 変換するテキスト
     * @param string|null $voiceId 使用する音声ID（省略時はデフォルト）
     * @param string $format 出力形式（mp3またはpcm）
     * @return array 音声データとメタデータ
     * @throws \Exception
     */
    public function synthesizeSpeech(string $text, ?string $voiceId = null, string $format = 'mp3'): array
    {
        try {
            // テキストを前処理（読み方修正）
            $processedText = $this->correctPronunciation($text);
            
            // キャッシュキーを生成
            $cacheKey = $this->generateCacheKey($processedText, $voiceId ?? $this->voiceId);
            
            // キャッシュから音声URLを取得
            $cachedUrl = Cache::get($cacheKey);
            if ($cachedUrl && Storage::disk('public')->exists($cachedUrl)) {
                return [
                    'audio_url' => Storage::disk('public')->url($cachedUrl),
                    'cache_hit' => true,
                    'text_length' => mb_strlen($text),
                ];
            }

            // SSML形式でテキストを送信
            $synthesizeParams = [
                'Text' => $processedText,
                'TextType' => 'ssml',
                'VoiceId' => $voiceId ?? $this->voiceId,
                'Engine' => $this->engine,
                'OutputFormat' => 'mp3'
            ];

            // Pollyで音声合成
            $result = $this->pollyClient->synthesizeSpeech($synthesizeParams);

            // 音声ファイルを保存
            $filename = $this->generateFilename($cacheKey);
            $audioStream = $result['AudioStream'];
            $contents = $audioStream->getContents();
            
            Storage::disk('public')->put($filename, $contents);

            // キャッシュに保存（24時間）
            Cache::put($cacheKey, $filename, now()->addDay());

            Log::info('Polly speech synthesis completed', [
                'text_length' => mb_strlen($text),
                'voice_id' => $voiceId ?? $this->voiceId,
                'filename' => $filename,
            ]);

            return [
                'audio_url' => Storage::disk('public')->url($filename),
                'cache_hit' => false,
                'text_length' => mb_strlen($text),
                'filename' => $filename,
            ];

        } catch (AwsException $e) {
            Log::error('AWS Polly error', [
                'error' => $e->getMessage(),
                'code' => $e->getAwsErrorCode(),
            ]);
            throw new \Exception('音声合成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * 利用可能な音声一覧を取得
     *
     * @return array
     */
    public function getAvailableVoices(): array
    {
        try {
            $result = $this->pollyClient->describeVoices([
                'LanguageCode' => 'ja-JP',
            ]);

            return collect($result['Voices'])->map(function ($voice) {
                return [
                    'id' => $voice['Id'],
                    'name' => $voice['Name'],
                    'gender' => $voice['Gender'],
                    'language_code' => $voice['LanguageCode'],
                    'supported_engines' => $voice['SupportedEngines'] ?? [],
                ];
            })->toArray();

        } catch (AwsException $e) {
            Log::error('Failed to get available voices', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * 観光地名などの読み方を修正
     *
     * @param string $text
     * @return string
     */
    private function correctPronunciation(string $text): string
    {
        // HTMLタグを削除
        $text = strip_tags($text);
        
        // 先に特殊文字をエスケープ
        $text = htmlspecialchars($text, ENT_XML1, 'UTF-8');
        
        // データベースから読み方修正データを取得（キャッシュ付き）
        $corrections = Cache::remember('pronunciation_corrections', 3600, function () {
            return PronunciationCorrection::active()
                ->ordered()
                ->get();
        });

        // データベースの設定に基づいて置換（エスケープ済みテキストに対して）
        foreach ($corrections as $correction) {
            $escapedOriginal = htmlspecialchars($correction->original_text, ENT_XML1, 'UTF-8');
            $ssml = $correction->getSSMLSubstitution();
            
            // SSML要素内では置換しない（既に置換済みの部分を避ける）
            $text = preg_replace_callback('/(<sub[^>]*>.*?<\/sub>)|(' . preg_quote($escapedOriginal, '/') . ')/', 
                function($matches) use ($ssml) {
                    // SSML要素の場合はそのまま返す
                    if (!empty($matches[1])) {
                        return $matches[1];
                    }
                    // 通常のテキストの場合は置換
                    return $ssml;
                }, $text);
        }
        
        // SSMLで包む
        return sprintf('<speak>%s</speak>', $text);
    }



    /**
     * キャッシュキーを生成
     *
     * @param string $text
     * @param string $voiceId
     * @return string
     */
    private function generateCacheKey(string $text, string $voiceId): string
    {
        return 'polly_audio_' . md5($text . $voiceId . $this->engine);
    }

    /**
     * ファイル名を生成
     *
     * @param string $cacheKey
     * @return string
     */
    private function generateFilename(string $cacheKey): string
    {
        return 'audio/polly/' . $cacheKey . '.mp3';
    }

    /**
     * 音声ファイルを削除
     *
     * @param string $filename
     * @return bool
     */
    public function deleteAudioFile(string $filename): bool
    {
        try {
            return Storage::delete($filename);
        } catch (\Exception $e) {
            Log::error('Failed to delete audio file', [
                'filename' => $filename,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * キャッシュをクリア
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::flush();
        
        // 音声ファイルも削除
        $files = Storage::files('audio/polly');
        foreach ($files as $file) {
            Storage::delete($file);
        }
        
        Log::info('Polly cache cleared');
    }
}