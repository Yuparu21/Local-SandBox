<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Tests\TestCase;

class VerifyEmailEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: 【正常系】メールアドレス確認が成功することを確認
     */
    public function test_verify_email_success(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $token = Str::random(128);
        EmailVerification::create([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $response = $this->withSession([])->postJson('/api/email/verify', [
            'token' => $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'メールアドレスの確認が完了しました。',
                'user' => [
                    'email' => 'test@example.com',
                ],
            ]);

        // DBでemail_verified_atが更新されたことを確認
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        // 確認トークンが削除されたことを確認
        $this->assertDatabaseMissing('email_verifications', [
            'email' => 'test@example.com',
        ]);
        
        // メール確認後、自動的にログイン状態になっていることを確認
        $this->assertAuthenticated();
        $this->assertEquals($user->id, auth()->id());
    }

    /**
     * Test: 【異常系】tokenが空でバリデーションエラー
     */
    public function test_verify_email_validation_error_empty_token(): void
    {
        $response = $this->postJson('/api/email/verify', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }

    /**
     * Test: 【異常系】tokenの長さが不正でバリデーションエラー
     */
    public function test_verify_email_validation_error_invalid_token_length(): void
    {
        $response = $this->postJson('/api/email/verify', [
            'token' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token'])
            ->assertJson([
                'errors' => [
                    'token' => ['確認トークンの形式が不正です。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】無効なトークンでエラー
     */
    public function test_verify_email_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        EmailVerification::create([
            'email' => 'test@example.com',
            'token' => Hash::make('valid-token'),
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('/api/email/verify', [
            'token' => Str::random(128), // 無効なトークン
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'token' => ['確認リンクが無効または期限切れです。'],
                ],
            ]);

        // email_verified_atは更新されないことを確認
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test: 【異常系】期限切れトークンでエラー
     */
    public function test_verify_email_expired_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        $token = Str::random(128);
        EmailVerification::create([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => Carbon::now()->subHours(25), // 25時間前
        ]);

        $response = $this->postJson('/api/email/verify', [
            'token' => $token,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'token' => ['確認リンクが無効または期限切れです。'],
                ],
            ]);

        // email_verified_atは更新されないことを確認
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test: 【異常系】既に確認済みのメールアドレスでエラー
     */
    public function test_verify_email_already_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => Carbon::now(),
        ]);

        $token = Str::random(128);
        EmailVerification::create([
            'email' => 'test@example.com',
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $response = $this->postJson('/api/email/verify', [
            'token' => $token,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'token' => ['このメールアドレスは既に確認済みです。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】メール未確認ユーザーはログインできない
     */
    public function test_unverified_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Test1234!@#'),
            'email_verified_at' => null,
        ]);

        $response = $this->withHeaders([
            'Referer' => 'http://localhost:3000',
            'Accept' => 'application/json',
        ])->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['メールアドレスの確認が完了していません。登録時に送信されたメールを確認してください。'],
                ],
            ]);

        $this->assertGuest();
    }

    /**
     * Test: 【正常系】メール確認後はログインできる
     */
    public function test_verified_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Test1234!@#'),
            'email_verified_at' => Carbon::now(),
        ]);

        $response = $this->withHeaders([
            'Referer' => 'http://localhost:3000',
            'Accept' => 'application/json',
        ])->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'ログインに成功しました。',
            ]);

        $this->assertAuthenticated();
    }
}
