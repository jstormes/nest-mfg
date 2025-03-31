<?php

declare(strict_types=1);

use App\Application\Actions\Home\HomeAction;
use App\Application\Actions\Job\JobAction;
use App\Application\Settings\SettingsInterface;
use App\Application\View\View;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        View::class => function (ContainerInterface $c) {
            return new View();
        },
        HomeAction::class => function (ContainerInterface $c) {
            return new HomeAction(
                $c->get(LoggerInterface::class),
                $c->get(View::class)
            );
        },
        JobAction::class => function (ContainerInterface $c) {
            return new JobAction(
                $c->get(LoggerInterface::class),
                $c->get(View::class)
            );
        },
    ]);
};
