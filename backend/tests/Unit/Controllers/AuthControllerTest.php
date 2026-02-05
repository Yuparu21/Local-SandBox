<?php

namespace Tests\Unit\Controllers;

use Mockery;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
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
}
