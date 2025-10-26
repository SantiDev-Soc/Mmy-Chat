<?php

use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';
