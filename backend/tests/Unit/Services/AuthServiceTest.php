<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use App\Services\AuthService;
use App\Models\User;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuthService $authService;

    public function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    /**
     * テスト用のアクティブユーザーを作成
     * @param string $email
     * @param string $password
     * @return User
     */
    private function createActiveUser(string $email = 'test@example.com', string $password = 'test'): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ]);
    }

    /**
     * テスト用のsuspendedユーザーを作成
     * @param string $email
     * @param string $password
     * @return User
     */
    private function createSuspendedUser(string $email = 'test@example.com', string $password = 'test'): User
    {
        return User::factory()->create([
            'email' => $email,
            'password' => $password,
            'status' => 'suspended',
        ]);
    }

    /** 
     * Test: 【正常系】ValidateUserStatus - アクティブなユーザーの場合、ValidationExceptionがスローされないことを確認
     */
    public function test_validateUserStatus_active(): void
    {
        $user = $this->createActiveUser();

        // 例外が投げられないことを確認
        $this->authService->validateUserStatus($user);
        $this->assertTrue(true);
    }

    /** 
     * Test: 【異常系】ValidateUserStatus - 非アクティブなユーザーの場合、ValidationExceptionがスローされることを確認
     */
    public function test_validateUserStatus_inactive(): void
    {
        $user = $this->createSuspendedUser();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('このアカウントは現在利用できません。');

        $this->authService->validateUserStatus($user);
    }

    /** 
     * Test: 【正常系】Login - 正しい認証情報でログインできることを確認
     */
    public function test_login_with_valid_credentials(): void
    {
        $this->createActiveUser();

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'test',
        ];

        $result = $this->authService->login($credentials);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals('active', $result->status);
    }

    /** 
     * Test: 【異常系】Login - 誤った認証情報(email誤り)でログインできないことを確認
     */
    public function test_login_with_invalid_email_credentials(): void
    {
        $this->createActiveUser();

        $credentials = [
            'email' => 'invalid-email@example.com',
            'password' => 'test',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('入力されたメールアドレスまたはパスワードが正しくありません。');

        $this->authService->login($credentials);
    }

    /** 
     * Test: 【異常系】Login - 誤った認証情報(password誤り)でログインできないことを確認
     */
    public function test_login_with_invalid_password_credentials(): void
    {
        $this->createActiveUser();

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'invalid-password',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('入力されたメールアドレスまたはパスワードが正しくありません。');

        $this->authService->login($credentials);
    }

    /** 
     * Test: 【異常系】Login - 誤った認証情報(email&password誤り)でログインできないことを確認
     */
    public function test_login_with_invalid_email_and_password_credentials(): void
    {
        $this->createActiveUser();

        $credentials = [
            'email' => 'invalid-email@example.com',
            'password' => 'invalid-password',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('入力されたメールアドレスまたはパスワードが正しくありません。');

        $this->authService->login($credentials);
    }

    /** 
     * Test: 【正常系】Logout - ログアウト処理が正常に実行されることを確認
     */
    public function test_logout(): void
    {
        $this->createActiveUser();

        $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'test',
        ]);

        // 例外が投げられないことを確認
        $this->authService->logout();
        $this->assertTrue(true);
    }

    /**
     * Test: 【異常系】Logout - ログアウト後に認証が解除されていることを確認
     */
    public function test_logout_clear_authentication(): void
    {
        $this->createActiveUser();

        $this->authService->login([
            'email' => 'test@example.com',
            'password' => 'test',
        ]);
        $this->assertTrue(Auth::guard('web')->check());

        $this->authService->logout();

        $this->assertFalse(Auth::guard('web')->check());
    }

    /**
     * Test: 【正常系】ValidateEmailVerified - メール確認済みユーザーの場合、例外がスローされないことを確認
     */
    public function test_validateEmailVerified_verified_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->authService->validateEmailVerified($user);
        $this->assertTrue(true);
    }

    /**
     * Test: 【異常系】ValidateEmailVerified - メール未確認ユーザーの場合、ValidationExceptionがスローされることを確認
     */
    public function test_validateEmailVerified_unverified_user(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('メールアドレスの確認が完了していません。登録時に送信されたメールを確認してください。');

        $this->authService->validateEmailVerified($user);
    }

    /**
     * Test: 【正常系】Register - 会員登録が成功することを確認
     */
    public function test_register_success(): void
    {
        $data = [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
        ];

        $user = $this->authService->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('山田太郎', $user->name);
        $this->assertEquals('ヤマダタロウ', $user->kana);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);

        // DBに保存されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
        ]);

        // メール確認トークンが保存されていることを確認
        $this->assertDatabaseHas('email_verifications', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test: 【正常系】VerifyEmail - メール確認が成功することを確認
     */
    public function test_verifyEmail_success(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $token = \Illuminate\Support\Str::random(128);
        \App\Models\EmailVerification::create([
            'email' => 'test@example.com',
            'token' => \Illuminate\Support\Facades\Hash::make($token),
            'created_at' => now(),
        ]);

        $result = $this->authService->verifyEmail(['token' => $token]);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertNotNull($result->email_verified_at);

        // トークンが削除されていることを確認
        $this->assertDatabaseMissing('email_verifications', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test: 【異常系】VerifyEmail - 無効なトークンでValidationExceptionがスローされることを確認
     */
    public function test_verifyEmail_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        \App\Models\EmailVerification::create([
            'email' => 'test@example.com',
            'token' => \Illuminate\Support\Facades\Hash::make('valid-token'),
            'created_at' => now(),
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('確認リンクが無効または期限切れです。');

        $this->authService->verifyEmail(['token' => \Illuminate\Support\Str::random(128)]);
    }

    /**
     * Test: 【異常系】VerifyEmail - 既に確認済みでValidationExceptionがスローされることを確認
     */
    public function test_verifyEmail_already_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $token = \Illuminate\Support\Str::random(128);
        \App\Models\EmailVerification::create([
            'email' => 'test@example.com',
            'token' => \Illuminate\Support\Facades\Hash::make($token),
            'created_at' => now(),
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('このメールアドレスは既に確認済みです。');

        $this->authService->verifyEmail(['token' => $token]);
    }
}
