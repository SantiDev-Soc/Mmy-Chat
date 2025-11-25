<?php
declare(strict_types=1);

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/health', static function () {
    return response()->json(['status' => 'ok']);
});


Route::post('/messages', [MessageController::class, 'store']);

Route::get('/messages/{contactId}', [MessageController::class, 'getMessagesWithContact'])
    ->whereUuid('contactId');

Route::get('/conversations/{userId}', [MessageController::class, 'getConversations'])
    ->whereUuid('userId');

Route::post('/messages/read', [MessageController::class, 'messagesRead']);

Route::delete('/conversations/{contactId}', [MessageController::class, 'deleteConversation'])
    ->whereUuid('contactId');
