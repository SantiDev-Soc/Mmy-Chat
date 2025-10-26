<?php
declare(strict_types=1);

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;


Route::post('/messages', [MessageController::class, 'store']);


