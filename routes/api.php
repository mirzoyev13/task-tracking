<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::prefix('v1')->group(function () {
    Route::apiResource('tasks', TaskController::class);
});
