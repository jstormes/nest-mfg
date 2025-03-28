<?php

declare(strict_types=1);

namespace Tests\Application\Handlers;

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

class ShutdownHandlerTest extends TestCase
{
    private ServerRequestInterface $request;
    private HttpErrorHandler $errorHandler;
    private ResponseInterface $response;
    private ResponseEmitter $responseEmitter;

    protected function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->errorHandler = $this->createMock(HttpErrorHandler::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseEmitter = $this->createMock(ResponseEmitter::class);
    }

    public function testHandleFatalError(): void
    {
        $error = [
            'type' => E_ERROR,
            'message' => 'Fatal error occurred',
            'file' => 'test.php',
            'line' => 123
        ];

        $handler = $this->createHandler(true);
        $handler->setErrorGetLastFunc(function () use ($error) {
            return $error;
        });

        $this->errorHandler->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->request,
                $this->callback(function ($exception) {
                    return $exception instanceof HttpInternalServerErrorException
                        && strpos($exception->getMessage(), 'FATAL ERROR: Fatal error occurred') !== false;
                }),
                true,
                false,
                false
            )
            ->willReturn($this->response);

        $this->responseEmitter->expects($this->once())
            ->method('emit')
            ->with($this->response);

        $handler->__invoke();
    }

    public function testHandleWarning(): void
    {
        $error = [
            'type' => E_WARNING,
            'message' => 'Warning occurred',
            'file' => 'test.php',
            'line' => 123
        ];

        $handler = $this->createHandler(true);
        $handler->setErrorGetLastFunc(function () use ($error) {
            return $error;
        });

        $this->errorHandler->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->request,
                $this->callback(function ($exception) {
                    return $exception instanceof HttpInternalServerErrorException
                        && strpos($exception->getMessage(), 'WARNING: Warning occurred') !== false;
                }),
                true,
                false,
                false
            )
            ->willReturn($this->response);

        $this->responseEmitter->expects($this->once())
            ->method('emit')
            ->with($this->response);

        $handler->__invoke();
    }

    public function testHandleNotice(): void
    {
        $error = [
            'type' => E_NOTICE,
            'message' => 'Notice occurred',
            'file' => 'test.php',
            'line' => 123
        ];

        $handler = $this->createHandler(true);
        $handler->setErrorGetLastFunc(function () use ($error) {
            return $error;
        });

        $this->errorHandler->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->request,
                $this->callback(function ($exception) {
                    return $exception instanceof HttpInternalServerErrorException
                        && strpos($exception->getMessage(), 'NOTICE: Notice occurred') !== false;
                }),
                true,
                false,
                false
            )
            ->willReturn($this->response);

        $this->responseEmitter->expects($this->once())
            ->method('emit')
            ->with($this->response);

        $handler->__invoke();
    }

    public function testHandleDefaultError(): void
    {
        $error = [
            'type' => E_CORE_ERROR,
            'message' => 'Generic error occurred',
            'file' => 'test.php',
            'line' => 123
        ];

        $handler = $this->createHandler(true);
        $handler->setErrorGetLastFunc(function () use ($error) {
            return $error;
        });

        $this->errorHandler->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->request,
                $this->callback(function ($exception) {
                    return $exception instanceof HttpInternalServerErrorException
                        && strpos($exception->getMessage(), 'FATAL ERROR: Generic error occurred') !== false;
                }),
                true,
                false,
                false
            )
            ->willReturn($this->response);

        $this->responseEmitter->expects($this->once())
            ->method('emit')
            ->with($this->response);

        $handler->__invoke();
    }

    public function testHandleErrorWithoutDisplayDetails(): void
    {
        $error = [
            'type' => E_ERROR,
            'message' => 'Generic error occurred',
            'file' => 'test.php',
            'line' => 123
        ];

        $handler = $this->createHandler(false);
        $handler->setErrorGetLastFunc(function () use ($error) {
            return $error;
        });

        $this->errorHandler->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->request,
                $this->callback(function ($exception) {
                    return $exception instanceof HttpInternalServerErrorException
                        && $exception->getMessage() === 'An error while processing your request. Please try again later.';
                }),
                false,
                false,
                false
            )
            ->willReturn($this->response);

        $this->responseEmitter->expects($this->once())
            ->method('emit')
            ->with($this->response);

        $handler->__invoke();
    }

    private function createHandler(bool $displayErrorDetails): ShutdownHandler
    {
        return new ShutdownHandler(
            $this->request,
            $this->errorHandler,
            $displayErrorDetails,
            $this->responseEmitter
        );
    }
} 