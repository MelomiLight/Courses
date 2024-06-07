<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\VerifyEmailController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([SetLocale::class])->group(function () {
    //Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    //show course
    Route::get('/course/{course}', [CourseController::class, 'show']);

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        //CRUD courses
        Route::post('/course', [CourseController::class, 'store']);
        Route::get('/course', [CourseController::class, 'index']);
        Route::patch('/course/{course}', [CourseController::class, 'update']);
        Route::delete('/course/{course}', [CourseController::class, 'delete']);

        //CRUD comments
        Route::post('/comment', [CommentController::class, 'store']);
        Route::get('/comment/{course}', [CommentController::class, 'index']);
        Route::patch('/comment/{comment}', [CommentController::class, 'update']);
        Route::delete('/comment/{comment}', [CommentController::class, 'delete']);
    });

    //email verification
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verify/resend', [VerifyEmailController::class, 'resendEmail'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});



