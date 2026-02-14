<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserEndpointTest extends TestCase
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
     * Test: 【正常系】認証済みユーザー情報が取得できることを確認
     */
    public function test_get_authenticated_user_info_success(): void
    {
        $user = $this->createActiveUser();
        $loginResponse = $this->actingAs($user)
            ->makeStatefulRequest('GET', '/api/user', []);
        
        $loginResponse->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
            ]);
    }
    
    /**
     * Test: 【異常系】未認証の場合、ユーザー情報が取得できないことを確認
     */
    public function test_get_authenticated_user_info_unauthenticated_fail(): void
    {
        $response = $this->makeStatefulRequest('GET', '/api/user', []);
        $response->assertStatus(401);
    }
}
