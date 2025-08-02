<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel backend is running',
        'timestamp' => now()
    ]);
});

// Health check endpoint for Railway
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'backend',
        'port' => request()->server('SERVER_PORT'),
        'timestamp' => now()
    ]);
});


// Route to serve audio files (WAV) - シンプルなストリーミング対応
Route::get('/storage/audio/polly/{filename}', function ($filename) {
    $path = "audio/polly/{$filename}";
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    
    $fullPath = Storage::disk('public')->path($path);
    
    return response()->file($fullPath, [
        'Content-Type' => 'audio/mpeg',
        'Accept-Ranges' => 'bytes',
        'Cache-Control' => 'public, max-age=86400',
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
        'Access-Control-Allow-Headers' => '*'
    ]);
})->where('filename', '.*\.mp3$');

// OPTIONSリクエスト対応
Route::options('/storage/audio/polly/{filename}', function () {
    return response('', 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
        'Access-Control-Allow-Headers' => '*'
    ]);
})->where('filename', '.*\.mp3$');

// 追加: 直接ダウンロード用ルート
Route::get('/download/audio/polly/{filename}', function ($filename) {
    $path = "audio/polly/{$filename}";
    
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    
    return Storage::disk('public')->download($path, $filename, [
        'Content-Type' => 'audio/mpeg'
    ]);
})->where('filename', '.*\.mp3$');

// API Documentation
Route::get('/docs', function() {
    return view('scribe.index');
})->name('scribe.docs');

Route::get('/docs/{path}', function($path) {
    if ($path === 'postman') {
        return response()->file(storage_path('app/private/scribe/collection.json'));
    }
    if ($path === 'openapi') {
        return response()->file(storage_path('app/private/scribe/openapi.yaml'));
    }
    return abort(404);
})->where('path', 'postman|openapi')->name('scribe.assets');
