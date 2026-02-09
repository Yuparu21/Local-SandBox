<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthService
{
    /**
     * ユーザーのステータスを検証
    * 
    * @param User $user
    * @return void
    * @throws ValidationException
    */
    public function validateUserStatus(User $user): void
    {
        if ($user->status !== 'active') {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => ['このアカウントは現在利用できません。'],
            ]);
        }
    }
    
    /**
     * ログイン処理
     * @param array $credentials
     * @return User $user
     * @throws ValidationException
     */
    public function login(array $credentials)
    {
        if (!Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['入力されたメールアドレスまたはパスワードが正しくありません。'],
            ]);
        }
        $user = Auth::guard('web')->user();
        $this->validateUserStatus($user);    
        
        return $user;
    }   

    /**
     * ログアウト処理
     * @return void
     */
    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}
