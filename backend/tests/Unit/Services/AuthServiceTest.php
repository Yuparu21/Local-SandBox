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
        $this->expectExceptionMessage('認証情報が正しくありません。');

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
        $this->expectExceptionMessage('認証情報が正しくありません。');

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
        $this->expectExceptionMessage('認証情報が正しくありません。');

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
}
