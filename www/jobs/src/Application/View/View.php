<?php

declare(strict_types=1);

namespace App\Application\View;

use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface as Response;

class View
{
    private Engine $engine;

    public function __construct(string $templatesPath = __DIR__ . '/../../../templates')
    {
        $this->engine = new Engine($templatesPath);
        $this->engine->setFileExtension('phtml');
    }

    public function render(Response $response, string $template, array $data = []): Response
    {
        $response->getBody()->write($this->engine->render($template, $data));
        return $response;
    }
} 