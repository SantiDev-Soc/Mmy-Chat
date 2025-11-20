<?php
declare(strict_types=1);

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/health', static function () {
    return response()->json(['status' => 'ok']);
});


Route::post('/messages', [MessageController::class, 'store']);

Route::get('/messages/{contactId}', [MessageController::class, 'getMessagesWithContact']);

Route::get('/conversations/{userId}', [MessageController::class, 'getConversations']);


