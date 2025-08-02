<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Contracts\AudioSynthesizerInterface;
use App\Services\MockAudioSynthesizer;
use App\Http\Controllers\Api\AudioGuideController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AudioSynthesizerTest extends TestCase
{
    /**
     * インターフェースのテスト例
     * 
     * @test
     */
    public function test_mock_audio_synthesizer_implements_interface()
    {
        $mockSynthesizer = new MockAudioSynthesizer();
        
        // インターフェースを実装していることを確認
        $this->assertInstanceOf(AudioSynthesizerInterface::class, $mockSynthesizer);
    }

    /**
     * モック音声合成のテスト
     * 
     * @test
     */
    public function test_mock_synthesize_speech()
    {
        $mockSynthesizer = new MockAudioSynthesizer();
        
        $result = $mockSynthesizer->synthesizeSpeech('テストテキスト', 'Takumi');
        
        // 期待する構造を持っているか確認
        $this->assertArrayHasKey('audio_url', $result);
        $this->assertArrayHasKey('cache_hit', $result);
        $this->assertArrayHasKey('text_length', $result);
        $this->assertArrayHasKey('mock', $result);
        
        // モックフラグが設定されているか確認
        $this->assertTrue($result['mock']);
        
        // テキスト長が正しいか確認
        $this->assertEquals(6, $result['text_length']);
    }

    /**
     * 利用可能な音声一覧のテスト
     * 
     * @test
     */
    public function test_get_available_voices()
    {
        $mockSynthesizer = new MockAudioSynthesizer();
        
        $voices = $mockSynthesizer->getAvailableVoices();
        
        // 音声が返されることを確認
        $this->assertIsArray($voices);
        $this->assertNotEmpty($voices);
        
        // 最初の音声の構造を確認
        $firstVoice = $voices[0];
        $this->assertArrayHasKey('id', $firstVoice);
        $this->assertArrayHasKey('name', $firstVoice);
        $this->assertArrayHasKey('gender', $firstVoice);
        $this->assertArrayHasKey('language_code', $firstVoice);
    }

    /**
     * 依存性注入のテスト例
     * 
     * @test
     */
    public function test_controller_with_mock_dependency()
    {
        // モックを作成
        $mockSynthesizer = new MockAudioSynthesizer();
        
        // コントローラーにモックを注入
        $controller = new AudioGuideController($mockSynthesizer);
        
        // コントローラーが正しく動作することを確認
        $this->assertInstanceOf(AudioGuideController::class, $controller);
    }

    /**
     * 音声IDの有効性チェックテスト
     * 
     * @test
     */
    public function test_voice_availability_check()
    {
        $mockSynthesizer = new MockAudioSynthesizer();
        
        // 存在する音声ID
        $this->assertTrue($mockSynthesizer->isVoiceAvailable('Takumi'));
        $this->assertTrue($mockSynthesizer->isVoiceAvailable('Tomoko'));
        
        // 存在しない音声ID
        $this->assertFalse($mockSynthesizer->isVoiceAvailable('NonExistentVoice'));
    }
}