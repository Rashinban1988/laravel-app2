<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 他の認証が必要なルートをここに追加
    Route::get('/organization', [OrganizationController::class, 'index']);
    Route::get('/organization/{id}', [OrganizationController::class, 'show']);
});

// メール認証関連のルート
Route::prefix('email')->group(function () {
    Route::get('/verify/{id}/{hash}', function (Request $request) {
        $user = \App\Models\User::find($request->route('id'));

        if (!$user || !hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => '無効なリンクです'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'すでに認証済みです'], 200);
        }

        $user->markEmailAsVerified();

        return Redirect::to(config('app.front_url') . '/auth/register-success');
    })->middleware('signed')->name('verification.verify');

    Route::post('/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => '確認メールを再送信しました'], 200);
    })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
});