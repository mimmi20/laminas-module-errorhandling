<?php

/**
 * This file is part of the mimmi20/laminas-module-errorhandling package.
 *
 * Copyright (c) 2020-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\ErrorHandling;

use ArrayAccess;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Override;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

final class Module implements BootstrapListenerInterface, ConfigProviderInterface, ServiceProviderInterface
{
    /**
     * @return array<array<array<string>>>
     * @phpstan-return array{service_manager: array{factories: array<class-string, class-string>}}
     *
     * @throws void
     */
    #[Override]
    public function getConfig(): array
    {
        return ['service_manager' => $this->getServiceConfig()];
    }

    /**
     * @return array<array<string>>
     * @phpstan-return array{factories: array<class-string, class-string>}
     *
     * @throws void
     */
    #[Override]
    public function getServiceConfig(): array
    {
        return ['factories' => [LogListener::class => LogListenerFactory::class]];
    }

    /**
     * @param EventInterface<object|string|null, array<mixed>|ArrayAccess|object> $e
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    #[Override]
    public function onBootstrap(EventInterface $e): void
    {
        if (!$e instanceof MvcEvent) {
            return;
        }

        $application = $e->getApplication();
        assert($application instanceof ApplicationInterface);

        $eventManager = $application->getEventManager();
        assert($eventManager instanceof EventManagerInterface);

        // get services
        $serviceManager = $application->getServiceManager();
        assert($serviceManager instanceof ServiceLocatorInterface);

        $logListener = $serviceManager->get(LogListener::class);
        assert($logListener instanceof ListenerAggregateInterface);

        $logListener->attach($eventManager, -2000);
    }
}
