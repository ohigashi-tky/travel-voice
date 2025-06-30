<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TestPollyController extends Controller
{
    public function test(): JsonResponse
    {
        try {
            // 手動でAWS SDKを読み込み
            require_once base_path('vendor/autoload.php');
            
            $pollyClient = new \Aws\Polly\PollyClient([
                'version' => 'latest',
                'region' => config('services.aws.polly_region', 'ap-northeast-1'),
                'credentials' => [
                    'key' => config('services.aws.key'),
                    'secret' => config('services.aws.secret'),
                ],
            ]);

            // 利用可能な音声を取得
            $result = $pollyClient->describeVoices([
                'LanguageCode' => 'ja-JP',
            ]);

            $voices = [];
            foreach ($result['Voices'] as $voice) {
                $voices[] = [
                    'id' => $voice['Id'],
                    'name' => $voice['Name'],
                    'gender' => $voice['Gender'],
                ];
            }

            // 簡単な音声合成テスト
            $testResult = $pollyClient->synthesizeSpeech([
                'OutputFormat' => 'mp3',
                'Text' => '<speak>こんにちは、AWS Pollyのテストです。</speak>',
                'TextType' => 'ssml',
                'VoiceId' => 'Takumi',
                'Engine' => 'neural',
            ]);

            $audioData = $testResult['AudioStream']->getContents();

            return response()->json([
                'success' => true,
                'message' => 'AWS Polly integration working!',
                'data' => [
                    'voices' => $voices,
                    'audio_size' => strlen($audioData),
                    'config' => [
                        'region' => config('services.aws.polly_region'),
                        'voice_id' => config('services.aws.polly_voice_id'),
                    ]
                ]
            ]);

        } catch (\Aws\Exception\AwsException $e) {
            Log::error('AWS Polly Error', [
                'message' => $e->getMessage(),
                'code' => $e->getAwsErrorCode(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'AWS Error: ' . $e->getMessage(),
                'error_code' => $e->getAwsErrorCode(),
            ], 500);

        } catch (\Exception $e) {
            Log::error('General Error', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}