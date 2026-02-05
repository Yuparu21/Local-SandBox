<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class HandleCorsTest extends TestCase
{
    /**
     * Test: CORSヘッダーが正しく設定されていることを確認する
     */
    public function test_cors_headers_are_set_correctly(): void
    {
        $middleware = new HandleCors();

        $request = Request::create('/api/test', 'POST');

        $nextMock = function ($req) {
            return new Response('Test Response', 200);
        };
        
        $response = $middleware->handle($request, $nextMock);

        $this->assertEquals(
            'http://localhost:3000',
            $response->headers->get('Access-Control-Allow-Origin')
        );
        $this->assertEquals(
            'GET, POST, PUT, DELETE, OPTIONS',
            $response->headers->get('Access-Control-Allow-Methods')
        );
        $this->assertEquals(
            'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN',
            $response->headers->get('Access-Control-Allow-Headers')
        );
        $this->assertEquals(
            'true',
            $response->headers->get('Access-Control-Allow-Credentials')
        );
    }
    
    /**
     * Test: リクエストが次のミドルウェアに渡されることを確認する
     */
    public function test_request_is_passed_to_next_middleware(): void
    {
        $middleware = new HandleCors();
        
        $request = Request::create('/api/test', 'POST');
        
        $nextCalled = false;
        $passedRequest = null;
        
        $nextMock = function ($req) use (&$nextCalled, &$passedRequest) {
            $nextCalled = true;
            $passedRequest = $req;
            return new Response('Success', 200);
        };
        
        $response = $middleware->handle($request, $nextMock);
        
        $this->assertTrue($nextCalled, 'next should be called.');
        $this->assertSame($request, $passedRequest, 'Same request object should be passed');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Success', $response->getContent());
    }
}
