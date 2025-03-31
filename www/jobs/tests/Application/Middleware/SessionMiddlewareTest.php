<?php

declare(strict_types=1);

namespace Tests\Application\Middleware;

use App\Application\Middleware\SessionMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

class SessionMiddlewareTest extends TestCase
{
    private SessionMiddleware $middleware;
    private Request $request;
    private RequestHandlerInterface $handler;

    protected function setUp(): void
    {
        $this->middleware = new SessionMiddleware();

        // Initialize request
        $uri = new Uri('', '', 80, '/');
        $headers = new Headers();
        $handle = fopen('php://temp', 'wb+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $this->request = new Request('GET', $uri, $headers, [], [], $stream);

        // Mock request handler
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    protected function tearDown(): void
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            unset($_SERVER['HTTP_AUTHORIZATION']);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testProcessWithoutAuthorization(): void
    {
        $expectedResponse = new Response();
        
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->request)
            ->willReturn($expectedResponse);

        $response = $this->middleware->process($this->request, $this->handler);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @runInSeparateProcess
     */
    public function testProcessWithAuthorization(): void
    {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer token123';
        
        $expectedResponse = new Response();
        
        $this->handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($request) {
                return $request->getAttribute('session') !== null;
            }))
            ->willReturn($expectedResponse);

        $response = $this->middleware->process($this->request, $this->handler);

        $this->assertSame($expectedResponse, $response);
    }
} 