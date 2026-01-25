<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
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
        
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['認証情報が正しくありません。'],
            ]);
        }
        
        $user = $request->user();
        
        if ($user->status !== 'active') {
            Auth::guard('web')->logout();
            throw ValidationException::withMessages([
                'email' => ['このアカウントは現在利用できません。'],
            ]);
        }
        
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
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json([
            'message' => 'ログアウトに成功しました。',
        ], 200);
    }
    
    /**
     * 現在のユーザ取得
     * @param Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
