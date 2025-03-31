<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Home;

use App\Application\Actions\Home\HomeAction;
use App\Application\View\View;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Response;
use Slim\Psr7\Uri;

class HomeActionTest extends TestCase
{
    private LoggerInterface $logger;
    private View $view;
    private HomeAction $action;
    private SlimRequest $request;
    private Response $response;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->view = $this->createMock(View::class);
        $this->action = new HomeAction($this->logger, $this->view);

        // Initialize request
        $uri = new Uri('', '', 80, '/');
        $headers = new Headers();
        $handle = fopen('php://temp', 'wb+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);
        $this->request = new SlimRequest('GET', $uri, $headers, [], [], $stream);

        // Initialize response
        $this->response = new Response();
    }

    public function testHomeActionRendersIndexTemplate(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $expectedData = [
            'activeJobs' => 5,
            'pendingJobs' => 3,
            'completedJobs' => 12,
            'recentJobs' => [
                [
                    'id' => 1,
                    'title' => 'Sample Job 1',
                    'status' => 'Active',
                    'status_color' => 'primary',
                    'created_at' => '2024-03-31 10:00:00'
                ],
                [
                    'id' => 2,
                    'title' => 'Sample Job 2',
                    'status' => 'Pending',
                    'status_color' => 'warning',
                    'created_at' => '2024-03-31 09:30:00'
                ],
                [
                    'id' => 3,
                    'title' => 'Sample Job 3',
                    'status' => 'Completed',
                    'status_color' => 'success',
                    'created_at' => '2024-03-31 09:00:00'
                ]
            ]
        ];

        $this->view->expects($this->once())
            ->method('render')
            ->with(
                $this->response,
                'home/index',
                $expectedData
            )
            ->willReturn($expectedResponse);

        $response = $this->action->__invoke($this->request, $this->response, []);

        $this->assertEquals($expectedResponse, $response);
    }

    public function testHomeActionConstructor(): void
    {
        $this->assertInstanceOf(HomeAction::class, $this->action);
    }
} 