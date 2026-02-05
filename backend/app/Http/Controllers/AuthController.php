<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

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
}
