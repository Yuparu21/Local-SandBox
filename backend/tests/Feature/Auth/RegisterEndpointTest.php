<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\EmailVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;
use Tests\TestCase;

class RegisterEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: 【正常系】会員登録が成功することを確認
     */
    public function test_register_success(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => '登録が完了しました。確認メールを送信しましたので、メールアドレスの確認を行ってください。',
                'user' => [
                    'name' => '山田太郎',
                    'kana' => 'ヤマダタロウ',
                    'email' => 'test@example.com',
                ],
            ]);

        // DBに保存されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'email_verified_at' => null,
        ]);

        // メール確認トークンが保存されていることを確認
        $this->assertDatabaseHas('email_verifications', [
            'email' => 'test@example.com',
        ]);

        // 確認メールが送信されたことを確認
        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    /**
     * Test: 【異常系】nameが空でバリデーションエラー
     */
    public function test_register_validation_error_empty_name(): void
    {
        $response = $this->postJson('/api/register', [
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test: 【異常系】nameが長すぎてバリデーションエラー
     */
    public function test_register_validation_error_name_too_long(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => str_repeat('あ', 51),
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test: 【異常系】kanaが空でバリデーションエラー
     */
    public function test_register_validation_error_empty_kana(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kana']);
    }

    /**
     * Test: 【異常系】kanaがひらがなでバリデーションエラー
     */
    public function test_register_validation_error_kana_hiragana(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'やまだたろう',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kana'])
            ->assertJson([
                'errors' => [
                    'kana' => ['フリガナは全角カタカナで入力してください。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】emailが空でバリデーションエラー
     */
    public function test_register_validation_error_empty_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: 【異常系】emailの形式が不正でバリデーションエラー
     */
    public function test_register_validation_error_invalid_email(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'invalid-email',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test: 【異常系】emailが重複でバリデーションエラー
     */
    public function test_register_validation_error_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['このメールアドレスは既に登録されています。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】passwordが短すぎてバリデーションエラー
     */
    public function test_register_validation_error_password_too_short(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1!',
            'password_confirmation' => 'Test1!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: 【異常系】passwordが弱すぎてバリデーションエラー（小文字なし）
     */
    public function test_register_validation_error_password_no_lowercase(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'TEST1234!@#',
            'password_confirmation' => 'TEST1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['パスワードは英大文字、小文字、数字、記号(@$!%*?&#)をそれぞれ1文字以上含む必要があります。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】passwordが弱すぎてバリデーションエラー（大文字なし）
     */
    public function test_register_validation_error_password_no_uppercase(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'test1234!@#',
            'password_confirmation' => 'test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: 【異常系】passwordが弱すぎてバリデーションエラー（数字なし）
     */
    public function test_register_validation_error_password_no_digit(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'TestTest!@#',
            'password_confirmation' => 'TestTest!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: 【異常系】passwordが弱すぎてバリデーションエラー（記号なし）
     */
    public function test_register_validation_error_password_no_symbol(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test12345678',
            'password_confirmation' => 'Test12345678',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test: 【異常系】passwordが長すぎてバリデーションエラー
     */
    public function test_register_validation_error_password_too_long(): void
    {
        $longPassword = 'Test1234!@#' . str_repeat('a', 100);
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['パスワードは72文字以内で入力してください。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】kanaが長すぎてバリデーションエラー
     */
    public function test_register_validation_error_kana_too_long(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => str_repeat('ア', 51),
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kana'])
            ->assertJson([
                'errors' => [
                    'kana' => ['フリガナは50文字以内で入力してください。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】emailが長すぎてバリデーションエラー
     */
    public function test_register_validation_error_email_too_long(): void
    {
        $longEmail = str_repeat('a', 250) . '@example.com'; // 256文字以上
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => $longEmail,
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['メールアドレスは255文字以内で入力してください。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】kanaに英数字が含まれてバリデーションエラー
     */
    public function test_register_validation_error_kana_alphanumeric(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダTaro',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Test1234!@#',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kana'])
            ->assertJson([
                'errors' => [
                    'kana' => ['フリガナは全角カタカナで入力してください。'],
                ],
            ]);
    }

    /**
     * Test: 【異常系】password_confirmationが一致しない
     */
    public function test_register_validation_error_password_not_confirmed(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => '山田太郎',
            'kana' => 'ヤマダタロウ',
            'email' => 'test@example.com',
            'password' => 'Test1234!@#',
            'password_confirmation' => 'Different123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJson([
                'errors' => [
                    'password' => ['パスワード確認が一致しません。'],
                ],
            ]);
    }
}
