<?php

declare(strict_types=1);

namespace App\Application\Actions\Job;

use App\Application\Actions\Action;
use App\Application\View\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

class JobAction extends Action
{
    public function __construct(
        LoggerInterface $logger,
        private readonly View $view
    ) {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        return $this->view->render($this->response, 'job/create', [
            'title' => 'Create New Job'
        ]);
    }
} 