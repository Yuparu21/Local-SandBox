<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * ステートフルなリクエストを作成
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function makeStatefulRequest(string $method, string $uri, array $data = [])
    {
        return $this->withHeaders([
            'Referer' => 'http://localhost:3000',
            'Accept' => 'application/json',
        ])->json($method, $uri, $data);
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
     * Test: 【正常系】正常にログインできることを確認
     */
    public function test_login_success(): void
    {
        $this->createActiveUser();
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'test',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'ログインに成功しました。',
                'user' => [
                    'email' => 'test@example.com',
                    'status' => 'active',
                ],
            ]);
        $this->assertAuthenticated();
    }

    /**
     * Test: 【異常系】emailが空のため、バリデーションエラーになることを確認
     */
    public function test_login_validation_error_empty_email(): void
    {
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'password' => 'test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: 【異常系】passwordが空のため、バリデーションエラーになることを確認
     */
    public function test_login_validation_error_empty_password(): void
    {
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: 【異常系】emailの形式が不正なため、バリデーションエラーになることを確認
     */
    public function test_login_validation_error_invalid_email_format(): void
    {
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'invalid-email-format',
            'password' => 'test',
        ]); 
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: 【異常系】存在しないユーザーでログインできないことを確認
     */
    public function test_login_non_existent_user(): void
    {
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'test',
        ]); 
        
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        '入力されたメールアドレスまたはパスワードが正しくありません。'
                    ],   
                ],
            ]);
        $this->assertGuest();
    }
    
    /**
     * Test: 【異常系】間違ったpasswordでログインできないことを確認
     */
    public function test_login_wrong_password(): void
    {
        $this->createActiveUser();
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);
        
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        '入力されたメールアドレスまたはパスワードが正しくありません。'
                    ],   
                ],
            ]);
        $this->assertGuest();
    }

    /**
     * Test: 【異常系】suspendedユーザーはログインできないことを確認
     */
    public function test_login_suspended_user(): void
    {
        $this->createSuspendedUser();
        $response = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'test',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'このアカウントは現在利用できません。'
                    ],   
                ],
            ]);
        $this->assertGuest();
    }
}
