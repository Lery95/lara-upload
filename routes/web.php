<?php

use Illuminate\Support\Facades\Route;
use App\Events\UploadStatusUpdated;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/uploads', function () {
    return view('uploads');
});

Route::get('/test-broadcast', function () {
    $fakeUpload = [
        'filename' => 'sample.csv',
        'status' => 'processing',
    ];

    broadcast(new UploadStatusUpdated($fakeUpload));
    return 'Broadcast sent!';
});

// Broadcast::routes(['middleware' => ['auth:sanctum']]);
// Route::post('/broadcasting/auth', function () {
//   return Auth::user();
// });

Broadcast::routes(['middleware' => []]); // ⚠️ No auth
