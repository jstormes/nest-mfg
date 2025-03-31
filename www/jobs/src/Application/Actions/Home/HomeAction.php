<?php

declare(strict_types=1);

namespace App\Application\Actions\Home;

use App\Application\Actions\Action;
use App\Application\View\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class HomeAction extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private readonly View $view
    ) {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        // TODO: Replace with actual data from your service layer
        $data = [
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

        return $this->view->render($this->response, 'home/index', $data);
    }
} 