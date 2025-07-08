<?php

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the AudioGuideController directly
$controller = new \App\Http\Controllers\Api\AudioGuideController(
    new \App\Services\PollyService()
);

// Use reflection to access the private getTouristSpotData method
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('getTouristSpotData');
$method->setAccessible(true);

echo "Testing spot ID 201 (清水寺):\n";
$result = $method->invoke($controller, 201);

if ($result) {
    echo "Data found for ID 201:\n";
    echo "Name: " . $result['name'] . "\n";
    echo "Description: " . $result['description'] . "\n";
    echo "ID in data: " . ($result['id'] ?? 'NOT SET') . "\n";
} else {
    echo "No data found for ID 201\n";
}

echo "\nTesting spot ID 4 (銀座):\n";
$result4 = $method->invoke($controller, 4);

if ($result4) {
    echo "Data found for ID 4:\n";
    echo "Name: " . $result4['name'] . "\n";
    echo "Description: " . $result4['description'] . "\n";
    echo "ID in data: " . ($result4['id'] ?? 'NOT SET') . "\n";
} else {
    echo "No data found for ID 4\n";
}