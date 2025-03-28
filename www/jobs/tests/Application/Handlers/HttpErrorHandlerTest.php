<?php

declare(strict_types=1);

namespace Tests\Application\Handlers;

use App\Application\Handlers\HttpErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Interfaces\CallableResolverInterface;

class HttpErrorHandlerTest extends TestCase
{
    private ResponseFactoryInterface $responseFactory;
    private CallableResolverInterface $callableResolver;
    private LoggerInterface $logger;
    private ResponseInterface $response;
    private StreamInterface $stream;
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $this->callableResolver = $this->createMock(CallableResolverInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->stream = $this->createMock(StreamInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);

        $this->response->method('getBody')->willReturn($this->stream);
        $this->response->method('withHeader')->willReturnSelf();
        $this->response->method('withStatus')->willReturnSelf();
        $this->responseFactory->method('createResponse')->willReturn($this->response);
    }

    public function testHandleNotFoundException(): void
    {
        $exception = new HttpNotFoundException($this->request);
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 404
                    && $data['error']['type'] === 'RESOURCE_NOT_FOUND';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    public function testHandleMethodNotAllowedException(): void
    {
        $exception = new HttpMethodNotAllowedException($this->request);
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 405
                    && $data['error']['type'] === 'NOT_ALLOWED';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    public function testHandleUnauthorizedException(): void
    {
        $exception = new HttpUnauthorizedException($this->request);
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 401
                    && $data['error']['type'] === 'UNAUTHENTICATED';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    public function testHandleForbiddenException(): void
    {
        $exception = new HttpForbiddenException($this->request);
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 403
                    && $data['error']['type'] === 'INSUFFICIENT_PRIVILEGES';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    public function testHandleBadRequestException(): void
    {
        $exception = new HttpBadRequestException($this->request);
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 400
                    && $data['error']['type'] === 'BAD_REQUEST';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    public function testHandleNotImplementedException(): void
    {
        $exception = new HttpNotImplementedException($this->request);
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 501
                    && $data['error']['type'] === 'NOT_IMPLEMENTED';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    public function testHandleGenericException(): void
    {
        $exception = new \Exception('Test error');
        $handler = $this->createHandler();

        $this->stream->expects($this->once())
            ->method('write')
            ->with($this->callback(function ($json) {
                $data = json_decode($json, true);
                return $data['statusCode'] === 500
                    && $data['error']['type'] === 'SERVER_ERROR'
                    && $data['error']['description'] === 'Test error';
            }));

        $handler->__invoke($this->request, $exception, true, false, false);
    }

    private function createHandler(): HttpErrorHandler
    {
        return new HttpErrorHandler(
            $this->callableResolver,
            $this->responseFactory,
            $this->logger
        );
    }
} 