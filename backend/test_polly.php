<?php

require_once 'vendor/autoload.php';

use Aws\Polly\PollyClient;
use Aws\Exception\AwsException;

// 環境変数から設定を読み込み
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Testing AWS Polly Integration...\n";
echo "AWS_ACCESS_KEY_ID: " . ($_ENV['AWS_ACCESS_KEY_ID'] ? 'Set' : 'Not set') . "\n";
echo "AWS_SECRET_ACCESS_KEY: " . ($_ENV['AWS_SECRET_ACCESS_KEY'] ? 'Set' : 'Not set') . "\n";
echo "AWS_DEFAULT_REGION: " . ($_ENV['AWS_DEFAULT_REGION'] ?? 'Not set') . "\n\n";

try {
    $pollyClient = new PollyClient([
        'version' => 'latest',
        'region' => $_ENV['AWS_DEFAULT_REGION'] ?? 'ap-northeast-1',
        'credentials' => [
            'key' => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ],
    ]);

    echo "PollyClient created successfully!\n";

    // 利用可能な音声を取得
    $result = $pollyClient->describeVoices([
        'LanguageCode' => 'ja-JP',
    ]);

    echo "Available Japanese voices:\n";
    foreach ($result['Voices'] as $voice) {
        echo "- {$voice['Id']} ({$voice['Gender']}) - {$voice['Name']}\n";
    }

    // 簡単な音声合成テスト
    echo "\nTesting speech synthesis...\n";
    $testResult = $pollyClient->synthesizeSpeech([
        'OutputFormat' => 'mp3',
        'Text' => '<speak>こんにちは、テストです。</speak>',
        'TextType' => 'ssml',
        'VoiceId' => 'Takumi',
        'Engine' => 'neural',
    ]);

    $audioStream = $testResult['AudioStream'];
    $audioData = $audioStream->getContents();
    
    echo "Speech synthesis successful! Audio data size: " . strlen($audioData) . " bytes\n";

} catch (AwsException $e) {
    echo "AWS Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getAwsErrorCode() . "\n";
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}