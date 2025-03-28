<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\ResponseEmitter\ResponseEmitter;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpInternalServerErrorException;

class ShutdownHandler
{
    private ServerRequestInterface $request;
    private HttpErrorHandler $errorHandler;
    private bool $displayErrorDetails;
    private $errorGetLastFunc;
    private ResponseEmitter $responseEmitter;

    public function __construct(
        ServerRequestInterface $request,
        HttpErrorHandler $errorHandler,
        bool $displayErrorDetails,
        ?ResponseEmitter $responseEmitter = null
    ) {
        $this->request = $request;
        $this->errorHandler = $errorHandler;
        $this->displayErrorDetails = $displayErrorDetails;
        $this->errorGetLastFunc = 'error_get_last';
        $this->responseEmitter = $responseEmitter ?? new ResponseEmitter();
    }

    public function __invoke(): void
    {
        $error = ($this->errorGetLastFunc)();

        if (!is_array($error)) {
            return;
        }

        $message = $this->getErrorMessage($error);
        $exception = new HttpInternalServerErrorException(
            $this->request,
            $message
        );

        $response = $this->errorHandler->__invoke(
            $this->request,
            $exception,
            $this->displayErrorDetails,
            false,
            false,
        );

        $this->responseEmitter->emit($response);
    }

    private function getErrorMessage(array $error): string
    {
        if (!$this->displayErrorDetails) {
            return 'An error while processing your request. Please try again later.';
        }

        $message = $this->getErrorTypeString($error['type']) . ': ' . $error['message'];

        if (isset($error['file'])) {
            $message .= ' in ' . $error['file'];
        }

        if (isset($error['line'])) {
            $message .= ' on line ' . $error['line'];
        }

        return $message;
    }

    private function getErrorTypeString(int $type): string
    {
        switch ($type) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
                return 'FATAL ERROR';
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                return 'ERROR';
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return 'WARNING';
            case E_NOTICE:
            case E_USER_NOTICE:
                return 'NOTICE';
            case E_STRICT:
                return 'STRICT';
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return 'DEPRECATED';
            default:
                return 'ERROR';
        }
    }

    /**
     * For testing purposes only
     */
    public function setErrorGetLastFunc(callable $func): void
    {
        $this->errorGetLastFunc = $func;
    }
}
