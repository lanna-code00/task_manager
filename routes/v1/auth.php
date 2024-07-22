<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function() {
  Route::post('signup', 'signUpUser');
  Route::post('signin', 'signInUser');
});