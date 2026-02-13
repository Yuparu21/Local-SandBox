<?php

namespace Tests\Unit\Controllers;

use Mockery;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use App\Services\AuthService;
use App\Http\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
    
    /**
     * Test: login() がAuthServiceを呼ぶ & session regenerateを呼ぶことを確認
     */
    public function test_login_call_authService_and_session_regenerate(): void
    {   
        $authServiceLoginMock = Mockery::mock(AuthService::class);
        $authServiceLoginMock->shouldReceive('login')
            ->once()
            ->with([
                'email' => 'test@example.com',
                'password' => 'test'
                ])
            ->andReturn(new User(['id' => 1, 'email' => 'test@example.com']));
        $this->app->instance(AuthService::class, $authServiceLoginMock);
        
        $request = Request::create('/api/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);
        $sessionStoreMock = Mockery::mock(Store::class);
        $sessionStoreMock->shouldReceive('regenerate')->once();
        $request->setLaravelSession($sessionStoreMock);

        $controller = new AuthController($authServiceLoginMock);
        $response = $controller->login($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    /**
     * Test: login() が正しいレスポンスを返すことを確認
     */
    public function test_login_returns_correct_response(): void
    {
        $userMock = User::factory()->make([
            'id'       => 1,
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'test'
        ]);
        
        $authServiceLoginMock = Mockery::mock(AuthService::class);
        $authServiceLoginMock->shouldReceive('login')
            ->once()
            ->with([
                'email' => 'test@example.com',
                'password' => 'test'
                ])
            ->andReturn($userMock);
        $this->app->instance(AuthService::class, $authServiceLoginMock);
        
        $request = Request::create('/api/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'test'
        ]);
        $sessionStoreMock = Mockery::mock(Store::class);
        $sessionStoreMock->shouldReceive('regenerate')->once();
        $request->setLaravelSession($sessionStoreMock);
        
        $controller = new AuthController($authServiceLoginMock);
        $response = $controller->login($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('ログインに成功しました。', $data['message']);
        $this->assertEquals($userMock->id, $data['user']['id']);
        $this->assertEquals($userMock->name, $data['user']['name']);
        $this->assertEquals($userMock->email, $data['user']['email']);
    }
    
    /** 
     * Test: logout() がAuthServiceのlogoutを呼ぶことを確認
     */
    public function test_logout_calls_authService_logout(): void
    {
        $authServiceLogoutMock = Mockery::mock(AuthService::class);
        $authServiceLogoutMock->shouldReceive('logout')
            ->once();
        $this->app->instance(AuthService::class, $authServiceLogoutMock);
        
        $sessionStoreMock = Mockery::mock(Store::class);
        $sessionStoreMock->shouldReceive('invalidate')->once();
        $sessionStoreMock->shouldReceive('regenerateToken')->once();
        
        $request = Request::create('/api/logout', 'POST');
        $request->setLaravelSession($sessionStoreMock);
        
        $controller = new AuthController($authServiceLogoutMock);
        $response = $controller->logout($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    /**
     * Test: logout() が正しいレスポンスを返すことを確認
     */
    public function test_logout_returns_correct_response(): void
    {
        $authServiceLogoutMock = Mockery::mock(AuthService::class);
        $authServiceLogoutMock->shouldReceive('logout')
            ->once();
        $this->app->instance(AuthService::class, $authServiceLogoutMock);
        
        $sessionStoreMock = Mockery::mock(Store::class);
        $sessionStoreMock->shouldReceive('invalidate')->once();
        $sessionStoreMock->shouldReceive('regenerateToken')->once();
        
        $request = Request::create('/api/logout', 'POST');
        $request->setLaravelSession($sessionStoreMock);
        
        $controller = new AuthController($authServiceLogoutMock);
        $response = $controller->logout($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('ログアウトしました。', $data['message']);
    }
    
    /**
     * Test: user() が認証済みユーザー情報を返すことを確認
     */
    public function test_user_returns_authenticated_user_info(): void
    {
        $userMock = User::factory()->make([
            'id'       => 1,
            'name'     => 'Test User',
            'email'    => 'test@example.com',
        ]);
        
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('user')
            ->once()
            ->andReturn($userMock);
        
        $authServiceMock = Mockery::mock(AuthService::class);
        $this->app->instance(AuthService::class, $authServiceMock);

        $controller = new AuthController($authServiceMock);
        $response = $controller->user($requestMock);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals($userMock->id, $data['id']);
        $this->assertEquals($userMock->name, $data['name']);
        $this->assertEquals($userMock->email, $data['email']);
    }

    /**
     * Test: register() がAuthServiceを呼び正しいレスポンスを返すことを確認
     */
    public function test_register_calls_authService_and_returns_correct_response(): void
    {
        $userMock = User::factory()->make([
            'id'    => 1,
            'name'  => '山田太郎',
            'kana'  => 'ヤマダタロウ',
            'email' => 'test@example.com',
        ]);

        $authServiceMock = Mockery::mock(AuthService::class);
        $authServiceMock->shouldReceive('register')
            ->once()
            ->with([
                'name' => '山田太郎',
                'kana' => 'ヤマダタロウ',
                'email' => 'test@example.com',
                'password' => 'Test1234!@#',
            ])
            ->andReturn($userMock);
        $this->app->instance(AuthService::class, $authServiceMock);

        $requestMock = Mockery::mock(\App\Http\Requests\RegisterRequest::class);
        $requestMock->shouldReceive('validated')
            ->once()
            ->andReturn([
                'name' => '山田太郎',
                'kana' => 'ヤマダタロウ',
                'email' => 'test@example.com',
                'password' => 'Test1234!@#',
            ]);

        $controller = new AuthController($authServiceMock);
        $response = $controller->register($requestMock);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('登録が完了しました。確認メールを送信しましたので、メールアドレスの確認を行ってください。', $data['message']);
        $this->assertEquals($userMock->id, $data['user']['id']);
        $this->assertEquals($userMock->name, $data['user']['name']);
        $this->assertEquals($userMock->kana, $data['user']['kana']);
        $this->assertEquals($userMock->email, $data['user']['email']);
    }

    /**
     * Test: verifyEmail() がAuthServiceを呼び、自動ログイン処理を行い、正しいレスポンスを返すことを確認
     */
    public function test_verifyEmail_calls_authService_and_login_and_returns_correct_response(): void
    {
        $userMock = User::factory()->make([
            'id'    => 1,
            'name'  => '山田太郎',
            'kana'  => 'ヤマダタロウ',
            'email' => 'test@example.com',
        ]);

        $authServiceMock = Mockery::mock(AuthService::class);
        $authServiceMock->shouldReceive('verifyEmail')
            ->once()
            ->with(['token' => 'test-token-123'])
            ->andReturn($userMock);
        $this->app->instance(AuthService::class, $authServiceMock);

        // Auth ファサードで login() が呼ばれることを確認
        Auth::shouldReceive('login')
            ->once()
            ->with($userMock);

        $requestMock = Mockery::mock(\App\Http\Requests\VerifyEmailRequest::class);
        $requestMock->shouldReceive('validated')
            ->once()
            ->andReturn(['token' => 'test-token-123']);
        
        // セッションが利用可能な場合の再生成を確認
        $sessionStoreMock = Mockery::mock(Store::class);
        $sessionStoreMock->shouldReceive('regenerate')->once();
        $requestMock->shouldReceive('hasSession')
            ->once()
            ->andReturn(true);
        $requestMock->shouldReceive('session')
            ->once()
            ->andReturn($sessionStoreMock);

        $controller = new AuthController($authServiceMock);
        $response = $controller->verifyEmail($requestMock);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('メールアドレスの確認が完了しました。', $data['message']);
        $this->assertEquals($userMock->id, $data['user']['id']);
        $this->assertEquals($userMock->name, $data['user']['name']);
        $this->assertEquals($userMock->kana, $data['user']['kana']);
        $this->assertEquals($userMock->email, $data['user']['email']);
    }
}
