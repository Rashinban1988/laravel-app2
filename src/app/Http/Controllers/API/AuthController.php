<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterOrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
}
