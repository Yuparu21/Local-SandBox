<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyEmailRequest;

class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * ログイン処理
     * @param Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        
        $user = $this->authService->login($credentials);
        
        $request->session()->regenerate();
        
        return response()->json([
            'message' => 'ログインに成功しました。',
            'user' => $user,
        ], 200); 
    }
    
    /**
     * ログアウト処理
     * @param Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->authService->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json([
            'message' => 'ログアウトしました。',
        ], 200);
    }
    
    /**
     * 認証済みユーザー情報の取得
     * @param Request  $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * 会員登録処理
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => '登録が完了しました。確認メールを送信しましたので、メールアドレスの確認を行ってください。',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'kana' => $user->kana,
                'email' => $user->email,
            ],
        ], 201);
    }

    /**
     * メールアドレス確認処理
     * @param VerifyEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $this->authService->verifyEmail($request->validated());

        // メール確認後、自動的にログイン状態にする
        auth()->login($user);
        
        // セッションが利用可能な場合のみ再生成
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'message' => 'メールアドレスの確認が完了しました。',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'kana' => $user->kana,
                'email' => $user->email,
            ],
        ], 200);
    }
}
