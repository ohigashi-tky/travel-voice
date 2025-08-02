<?php

namespace App\Contracts;

/**
 * 音声合成サービスのインターフェース
 * 
 * このインターフェースを実装することで、異なる音声合成サービス
 * （Amazon Polly, Google TTS, Azure Speech等）を統一的に扱える
 */
interface AudioSynthesizerInterface
{
    /**
     * テキストを音声に変換
     *
     * @param string $text 変換するテキスト
     * @param string|null $voiceId 使用する音声ID（省略時はデフォルト）
     * @param string $format 出力形式（mp3, wav等）
     * @return array 音声データとメタデータ
     * @throws \Exception 音声合成に失敗した場合
     */
    public function synthesizeSpeech(string $text, ?string $voiceId = null, string $format = 'mp3'): array;

    /**
     * 利用可能な音声一覧を取得
     *
     * @return array 音声一覧（voice_id, name, language等）
     * @throws \Exception 音声一覧の取得に失敗した場合
     */
    public function getAvailableVoices(): array;

    /**
     * 指定した音声IDが利用可能かチェック
     *
     * @param string $voiceId チェックする音声ID
     * @return bool 利用可能な場合true
     */
    public function isVoiceAvailable(string $voiceId): bool;

    /**
     * 音声合成の設定を取得
     *
     * @return array 現在の設定（engine, region, format等）
     */
    public function getConfiguration(): array;
}