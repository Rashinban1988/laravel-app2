<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * 組織登録
     * @param RegisterOrganizationRequest $request
     * @return JsonResponse
     */
    public function register(RegisterOrganizationRequest $request)
    {
        // メールアドレスの重複チェック
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['error' => 'このメールアドレスは既に登録されています。'], 422);
        }

        $requestData = $request->all();

        try {
            DB::beginTransaction();
            $organizationData = [
                'name' => $requestData['name'],
                'phone_number' => $requestData['phone_number'],
            ];
            $organization = Organization::create($organizationData);

            // 組織登録時に、管理者ユーザーを作成
            $userData = [
                'sei' => $requestData['sei'],
                'mei' => $requestData['mei'],
                'email' => $requestData['email'],
                'password' => Hash::make($requestData['password']),
                'phone_number' => $requestData['phone_number'],
                'is_admin' => true,
                'is_retired' => false,
                'organization_id' => $organization->id,
            ];
            $user = User::create($userData);

            DB::commit();

            // メール認証リンクを送信
            $user->sendEmailVerificationNotification();

            $response = [
                'organization' => $organization,
                'user' => $user,
                'message' => 'メール認証リンクを送信しました。メールを確認してください。',
            ];
            return response()->json($response, 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * ログイン
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        Log::info($credentials);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token');

            return response()->json([
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ]);
        }

        return response()->json(['message' => 'ログイン情報が正しくありません'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ログアウトしました']);
    }
}
