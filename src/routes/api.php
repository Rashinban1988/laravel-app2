<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/register', [AuthController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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

        return response()->json(['message' => 'メールアドレスが認証されました'], 200);
    })->middleware('signed')->name('verification.verify');

    Route::post('/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => '確認メールを再送信しました'], 200);
    })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
});