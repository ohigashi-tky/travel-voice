<?php

namespace App\Services;

use App\Contracts\AudioSynthesizerInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * テスト・開発環境用のモック音声合成サービス
 * 
 * 実際の音声合成は行わず、ダミーファイルを返すことで
 * 外部API呼び出しを避け、開発効率とテスト実行速度を向上させる
 */
class MockAudioSynthesizer implements AudioSynthesizerInterface
{
    private array $mockVoices = [
        ['id' => 'Takumi', 'name' => 'Takumi', 'gender' => 'Male', 'language_code' => 'ja-JP'],
        ['id' => 'Tomoko', 'name' => 'Tomoko', 'gender' => 'Female', 'language_code' => 'ja-JP'],
        ['id' => 'Mizuki', 'name' => 'Mizuki', 'gender' => 'Female', 'language_code' => 'ja-JP'],
        ['id' => 'Kazuha', 'name' => 'Kazuha', 'gender' => 'Female', 'language_code' => 'ja-JP'],
    ];

    /**
     * ダミー音声ファイルを生成（実際には無音ファイル）
     *
     * @param string $text 変換するテキスト
     * @param string|null $voiceId 使用する音声ID
     * @param string $format 出力形式
     * @return array ダミー音声データとメタデータ
     */
    public function synthesizeSpeech(string $text, ?string $voiceId = null, string $format = 'mp3'): array
    {
        $voiceId = $voiceId ?? 'Takumi';
        
        // モック用の短い無音MP3ファイルを作成
        $filename = 'audio/mock/mock_audio_' . md5($text . $voiceId) . '.mp3';
        
        // 既にファイルが存在しない場合のみ作成
        if (!Storage::disk('public')->exists($filename)) {
            // 無音のダミーMP3ファイル（Base64エンコード済み）
            $dummyMp3 = base64_decode('SUQzAwAAAAABBlRYWFgAAAASAAADbGFuZwAAAAADZW5nAFRYWFgAAAAOAAADY29tAE1vY2sgQXVkaW8AVGFMQLMAB/8bJr8A');
            Storage::disk('public')->put($filename, $dummyMp3);
        }

        Log::info('Mock audio synthesis completed', [
            'text_length' => mb_strlen($text),
            'voice_id' => $voiceId,
            'filename' => $filename,
            'mock' => true,
        ]);

        return [
            'audio_url' => Storage::disk('public')->url($filename),
            'cache_hit' => false,
            'text_length' => mb_strlen($text),
            'filename' => $filename,
            'mock' => true,
        ];
    }

    /**
     * モック音声一覧を返す
     *
     * @return array モック音声一覧
     */
    public function getAvailableVoices(): array
    {
        return $this->mockVoices;
    }

    /**
     * 音声IDが利用可能かチェック（モック版）
     *
     * @param string $voiceId チェックする音声ID
     * @return bool 利用可能な場合true
     */
    public function isVoiceAvailable(string $voiceId): bool
    {
        foreach ($this->mockVoices as $voice) {
            if ($voice['id'] === $voiceId) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * モック設定を返す
     *
     * @return array モック設定
     */
    public function getConfiguration(): array
    {
        return [
            'service' => 'Mock Audio Synthesizer',
            'region' => 'mock-region',
            'output_format' => 'mp3',
            'voice_id' => 'Takumi',
            'engine' => 'mock',
            'version' => '1.0.0',
            'mock' => true,
        ];
    }

    /**
     * モック版キャッシュクリア（何もしない）
     *
     * @return void
     */
    public function clearCache(): void
    {
        Log::info('Mock cache cleared (no action taken)');
    }
}