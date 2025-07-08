<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Clearing Polly audio cache...\n";

try {
    // Clear cache
    Cache::flush();
    echo "Cache cleared successfully.\n";
    
    // Clear audio files
    if (Storage::disk('public')->exists('audio/polly')) {
        $files = Storage::disk('public')->files('audio/polly');
        foreach ($files as $file) {
            Storage::disk('public')->delete($file);
        }
        echo "Deleted " . count($files) . " audio files.\n";
    }
    
    echo "Cache clearing completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error clearing cache: " . $e->getMessage() . "\n";
    exit(1);
}