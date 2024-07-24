<?php
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
   Route::apiResource('task', TaskController::class);
   Route::get('fetch-all-tasks', [TaskController::class, 'fetchAllTasks'])->name('fetch-all-tasks');
   Route::get('fetch-my-tasks', [TaskController::class, 'fetchMyAssignedTasks'])->name('fetch-my-tasks');
});