<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'ff';
});

// Route::get('/', function () {
//     $user = User::factory()->create();
//     return (new ImportantStockUpdateNotification(Stock::first()))->toMail($user);
// });