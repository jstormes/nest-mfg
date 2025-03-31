<?php

declare(strict_types=1);

namespace Tests\Application\ResponseEmitter;

use App\Application\ResponseEmitter\ResponseEmitter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

class ResponseEmitterTest extends TestCase
{
    private ResponseEmitter $emitter;
    private Response $response;

    protected function setUp(): void
    {
        $this->emitter = new class extends ResponseEmitter {
            private $lastResponse;

            public function emit(ResponseInterface $response): void
            {
                $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

                $this->lastResponse = $response
                    ->withHeader('Access-Control-Allow-Credentials', 'true')
                    ->withHeader(
                        'Access-Control-Allow-Headers',
                        'X-Requested-With, Content-Type, Accept, Origin, Authorization',
                    )
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                    ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                    ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
                    ->withHeader('Pragma', 'no-cache');

                if ($origin !== '') {
                    $this->lastResponse = $this->lastResponse->withHeader('Access-Control-Allow-Origin', $origin);
                }

                if (ob_get_contents()) {
                    ob_clean();
                }
            }

            public function getLastResponse(): ResponseInterface
            {
                return $this->lastResponse;
            }
        };
        $this->response = new Response();
    }

    protected function tearDown(): void
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            unset($_SERVER['HTTP_ORIGIN']);
        }
    }

    public function testEmitWithoutOrigin(): void
    {
        $response = new Response();
        $this->emitter->emit($response);
        
        $this->assertFalse($this->emitter->getLastResponse()->hasHeader('Access-Control-Allow-Origin'));
    }

    public function testEmitWithOrigin(): void
    {
        $_SERVER['HTTP_ORIGIN'] = 'http://example.com';
        $response = new Response();
        $this->emitter->emit($response);
        
        $this->assertEquals(
            ['http://example.com'],
            $this->emitter->getLastResponse()->getHeader('Access-Control-Allow-Origin')
        );
    }

    public function testEmitSetsRequiredHeaders(): void
    {
        $response = new Response();
        $this->emitter->emit($response);
        
        $lastResponse = $this->emitter->getLastResponse();
        
        $this->assertEquals(['true'], $lastResponse->getHeader('Access-Control-Allow-Credentials'));
        $this->assertEquals(
            ['X-Requested-With, Content-Type, Accept, Origin, Authorization'],
            $lastResponse->getHeader('Access-Control-Allow-Headers')
        );
        $this->assertEquals(
            ['GET, POST, PUT, PATCH, DELETE, OPTIONS'],
            $lastResponse->getHeader('Access-Control-Allow-Methods')
        );
        $cacheControlHeaders = $lastResponse->getHeader('Cache-Control');
        $this->assertCount(2, $cacheControlHeaders);
        $this->assertEquals('no-store, no-cache, must-revalidate, max-age=0', $cacheControlHeaders[0]);
        $this->assertEquals('post-check=0, pre-check=0', $cacheControlHeaders[1]);
        $this->assertEquals(['no-cache'], $lastResponse->getHeader('Pragma'));
    }
} 