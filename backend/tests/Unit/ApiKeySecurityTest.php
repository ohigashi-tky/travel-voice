<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ApiKeySecurityTest extends TestCase
{
    /**
     * Test that .env.example files don't contain actual API keys
     */
    public function test_env_example_files_contain_placeholder_values(): void
    {
        // Check root .env.example
        $rootEnvExample = base_path('.env.example');
        if (file_exists($rootEnvExample)) {
            $content = file_get_contents($rootEnvExample);
            
            // Ensure no actual Google Maps API keys are present
            $this->assertStringNotContainsString('AIzaSy', $content, 'Root .env.example should not contain actual Google Maps API keys');
            
            // Ensure no actual Unsplash API keys are present  
            $this->assertStringNotContainsString('XdPmz-', $content, 'Root .env.example should not contain actual Unsplash API keys');
            
            // Ensure placeholder values are present
            $this->assertStringContainsString('GOOGLE_MAPS_API_KEY=your_api_key', $content, 'Root .env.example should contain placeholder for Google Maps API key');
            $this->assertStringContainsString('UNSPLASH_ACCESS_KEY=your_api_key', $content, 'Root .env.example should contain placeholder for Unsplash API key');
        }
        
        // Check frontend .env.example
        $frontendEnvExample = base_path('frontend/.env.example');
        if (file_exists($frontendEnvExample)) {
            $content = file_get_contents($frontendEnvExample);
            
            // Ensure no actual API keys are present
            $this->assertStringNotContainsString('AIzaSy', $content, 'Frontend .env.example should not contain actual API keys');
            
            // Ensure placeholder values are present
            $this->assertStringContainsString('your_google_maps_api_key_here', $content, 'Frontend .env.example should contain placeholder text');
        }
    }
    
    /**
     * Test that documentation doesn't contain actual API keys
     */
    public function test_documentation_contains_placeholder_values(): void
    {
        $googleMapsSetupDoc = base_path('docs/google-maps-setup.md');
        if (file_exists($googleMapsSetupDoc)) {
            $content = file_get_contents($googleMapsSetupDoc);
            
            // Ensure no actual API keys are present in documentation
            $this->assertStringNotContainsString('AIzaSy', $content, 'Documentation should not contain actual API keys');
            
            // Ensure placeholder values are present
            $this->assertStringContainsString('your_api_key', $content, 'Documentation should contain placeholder API key');
        }
    }
    
    /**
     * Test that Vue components use placeholder fallback values
     */
    public function test_vue_components_use_placeholder_fallbacks(): void
    {
        $componentPaths = [
            'frontend/components/EmbeddedGoogleMap.vue',
            'frontend/components/GoogleMapEmbed.vue', 
            'frontend/components/JapanGoogleMap.vue'
        ];
        
        foreach ($componentPaths as $componentPath) {
            $fullPath = base_path($componentPath);
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                
                // Ensure no actual API keys are present as fallbacks
                $this->assertStringNotContainsString('AIzaSy', $content, "$componentPath should not contain actual API keys as fallbacks");
                
                // Ensure placeholder fallback is present
                $this->assertStringContainsString("|| 'your_api_key'", $content, "$componentPath should use placeholder as fallback");
            }
        }
    }
}