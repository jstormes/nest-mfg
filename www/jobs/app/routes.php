<?php

declare(strict_types=1);

use App\Application\Actions\Home\HomeAction;
use App\Application\Actions\Job\JobAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', HomeAction::class);
    
    // Job routes
    $app->get('/jobs/create', JobAction::class);
    $app->post('/jobs/create', JobAction::class);
};
