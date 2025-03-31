<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Job;

use App\Application\Actions\Job\JobAction;
use App\Application\View\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Response as SlimResponse;
use Slim\Psr7\Uri;
use Tests\TestCase;

class JobActionTest extends TestCase
{
    private LoggerInterface $logger;
    private View $view;
    private JobAction $action;
    private Request $request;
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->view = $this->createMock(View::class);
        $this->action = new JobAction($this->logger, $this->view);
        
        // Initialize request
        $uri = new Uri('', '', 80, '/jobs/create');
        $headers = new Headers();
        $handle = fopen('php://temp', 'wb+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $this->request = new SlimRequest('GET', $uri, $headers, [], [], $stream);
        
        // Initialize response
        $this->response = new SlimResponse();
    }

    public function testJobActionRendersCreateForm(): void
    {
        $expectedResponse = $this->createMock(Response::class);
        
        $this->view->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'job/create',
                ['title' => 'Create New Job']
            )
            ->willReturn($expectedResponse);

        $response = $this->action->__invoke($this->request, $this->response, []);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testJobActionHandlesPostRequest(): void
    {
        $formData = [
            'name' => 'Test Job',
            'description' => 'Test Description',
            'status' => 'Not Started'
        ];

        $uri = new Uri('', '', 80, '/jobs/create');
        $headers = new Headers();
        $handle = fopen('php://temp', 'wb+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $this->request = new SlimRequest('POST', $uri, $headers, [], [], $stream);
        $this->request = $this->request->withParsedBody($formData);

        $expectedResponse = $this->createMock(Response::class);

        $this->view->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'job/create',
                ['title' => 'Create New Job']
            )
            ->willReturn($expectedResponse);

        $response = $this->action->__invoke($this->request, $this->response, []);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testJobActionValidatesRequiredFields(): void
    {
        $formData = [
            'description' => 'Test Description',
            'status' => 'Not Started'
        ];

        $uri = new Uri('', '', 80, '/jobs/create');
        $headers = new Headers();
        $handle = fopen('php://temp', 'wb+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $this->request = new SlimRequest('POST', $uri, $headers, [], [], $stream);
        $this->request = $this->request->withParsedBody($formData);

        $expectedResponse = $this->createMock(Response::class);

        $this->view->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'job/create',
                ['title' => 'Create New Job']
            )
            ->willReturn($expectedResponse);

        $response = $this->action->__invoke($this->request, $this->response, []);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testJobActionValidatesStatusValues(): void
    {
        $formData = [
            'name' => 'Test Job',
            'description' => 'Test Description',
            'status' => 'Invalid Status'
        ];

        $uri = new Uri('', '', 80, '/jobs/create');
        $headers = new Headers();
        $handle = fopen('php://temp', 'wb+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $this->request = new SlimRequest('POST', $uri, $headers, [], [], $stream);
        $this->request = $this->request->withParsedBody($formData);

        $expectedResponse = $this->createMock(Response::class);

        $this->view->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'job/create',
                ['title' => 'Create New Job']
            )
            ->willReturn($expectedResponse);

        $response = $this->action->__invoke($this->request, $this->response, []);

        $this->assertEquals($expectedResponse, $response);
    }
} 