<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutEndpointTest extends TestCase
{
    use RefreshDatabase;

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
     * @return \App\Models\User
     */
    private function createActiveUser(string $email = 'test@example.com', string $password = 'test')
    {
        return \App\Models\User::factory()->create([
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ]);
    }

    /**
     * Test: 【正常系】ログアウトが成功することを確認
     */
    public function test_logout_success(): void
    {
        $user = $this->createActiveUser();
        $loginResponse = $this->makeStatefulRequest('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'test',
        ]);
        $loginResponse->assertStatus(200);

        $logoutResponse = $this->makeStatefulRequest('POST', '/api/logout', []);
        $logoutResponse->assertStatus(200)
            ->assertJson([
                'message' => 'ログアウトしました。',
            ]);
    }

    /**
     * Test: 【異常系】未認証の場合、ログアウトエンドポイントにアクセスできないことを確認
     */
    public function test_logout_unauthenticated(): void
    {
        $response = $this->makeStatefulRequest('POST', '/api/logout', []);
        $response->assertStatus(401);
    }
}
