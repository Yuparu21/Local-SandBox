<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\EmailVerification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;

class AuthService
{
    /**
     * メール確認トークンの長さ（文字数）
     */
    private const EMAIL_VERIFICATION_TOKEN_LENGTH = 128;

    /**
     * メール確認トークンの有効期限（時間）
     */
    private const EMAIL_VERIFICATION_EXPIRY_HOURS = 24;

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
     * メール確認済みかを検証
     * 
     * @param User $user
     * @return void
     * @throws ValidationException
     */
    public function validateEmailVerified(User $user): void
    {
        if (is_null($user->email_verified_at)) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => ['メールアドレスの確認が完了していません。登録時に送信されたメールを確認してください。'],
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
        $this->validateEmailVerified($user);
        
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

    /**
     * 会員登録処理
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'kana' => $data['kana'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $this->sendVerificationEmail($user);

        return $user;
    }

    /**
     * 確認メール送信
     * @param User $user
     * @return void
     */
    protected function sendVerificationEmail(User $user): void
    {
        $token = Str::random(self::EMAIL_VERIFICATION_TOKEN_LENGTH);

        EmailVerification::updateOrCreate(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $user->notify(new VerifyEmailNotification($token, $user->name));
    }

    /**
     * メールアドレス確認処理
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function verifyEmail(array $data): User
    {
        $verifications = EmailVerification::where('created_at', '>', Carbon::now()->subHours(self::EMAIL_VERIFICATION_EXPIRY_HOURS))->get();

        foreach ($verifications as $verification) {
            if (Hash::check($data['token'], $verification->token)) {
                $user = User::where('email', $verification->email)->first();

                if (!$user) {
                    throw ValidationException::withMessages([
                        'token' => ['ユーザーが見つかりません。'],
                    ]);
                }

                if ($user->email_verified_at) {
                    throw ValidationException::withMessages([
                        'token' => ['このメールアドレスは既に確認済みです。'],
                    ]);
                }

                $user->email_verified_at = Carbon::now();
                $user->save();

                $verification->delete();

                return $user;
            }
        }

        throw ValidationException::withMessages([
            'token' => ['確認リンクが無効または期限切れです。'],
        ]);
    }
}
