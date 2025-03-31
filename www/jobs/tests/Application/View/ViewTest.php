<?php

declare(strict_types=1);

namespace Tests\Application\View;

use App\Application\View\View;
use League\Plates\Engine;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Response;

class ViewTest extends TestCase
{
    private View $view;
    private Response $response;

    protected function setUp(): void
    {
        $this->view = new View(__DIR__ . '/../../../templates');
        $this->response = new Response();
    }

    public function testConstructorWithDefaultPath(): void
    {
        $view = new View();
        $this->assertInstanceOf(View::class, $view);
    }

    public function testConstructorWithCustomPath(): void
    {
        $view = new View(__DIR__ . '/../../../templates');
        $this->assertInstanceOf(View::class, $view);
    }

    public function testRenderTemplate(): void
    {
        $template = 'home/index';
        $data = ['test' => 'value'];
        $renderedContent = '<html>Test Content</html>';

        // Create a mock stream that will be written to
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with($renderedContent);

        // Create a mock response that will return our mock stream
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        // Create a mock Engine that will return our rendered content
        $engine = $this->createMock(Engine::class);
        $engine->expects($this->once())
            ->method('render')
            ->with($template, $data)
            ->willReturn($renderedContent);

        // Set the mocked engine to our view using reflection
        $reflection = new \ReflectionClass(View::class);
        $property = $reflection->getProperty('engine');
        $property->setAccessible(true);
        $view = new View();
        $property->setValue($view, $engine);

        // Test the render method
        $result = $view->render($response, $template, $data);

        $this->assertSame($response, $result);
    }

    public function testRenderTemplateWithEmptyData(): void
    {
        $template = 'home/index';
        $renderedContent = '<html>Test Content</html>';

        // Create a mock stream that will be written to
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with($renderedContent);

        // Create a mock response that will return our mock stream
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        // Create a mock Engine that will return our rendered content
        $engine = $this->createMock(Engine::class);
        $engine->expects($this->once())
            ->method('render')
            ->with($template, [])
            ->willReturn($renderedContent);

        // Set the mocked engine to our view using reflection
        $reflection = new \ReflectionClass(View::class);
        $property = $reflection->getProperty('engine');
        $property->setAccessible(true);
        $view = new View();
        $property->setValue($view, $engine);

        // Test the render method
        $result = $view->render($response, $template);

        $this->assertSame($response, $result);
    }
} 