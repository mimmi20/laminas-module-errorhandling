<?php


namespace Mimmi20\ErrorHandling;

use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class Module implements BootstrapListenerInterface, ConfigProviderInterface, DependencyIndicatorInterface, ServiceProviderInterface
{
    /**
     * @return string[][][]
     * @phpstan-return array{service_manager: array{factories: array<class-string, class-string>}}
     *
     * @throws void
     */
    public function getConfig(): array
    {
        return ['service_manager' => $this->getServiceConfig()];
    }

    /**
     * @return string[][]
     * @phpstan-return array{factories: array<class-string, class-string>}
     * @throws void
     */
    public function getServiceConfig(): array
    {
        return ['factories' => [LogListener::class => LogListenerFactory::class]];
    }

    /**
     * @param EventInterface|MvcEvent $e
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function onBootstrap(EventInterface $e): void
    {
        if (! $e instanceof MvcEvent) {
            return;
        }

        $application = $e->getApplication();
        assert($application instanceof ApplicationInterface);

        // add listeners
        $eventManager = $application->getEventManager();
        assert($eventManager instanceof EventManagerInterface);

        // get services
        $serviceManager = $application->getServiceManager();
        assert($serviceManager instanceof ServiceLocatorInterface);

        $logListener = $serviceManager->get(LogListener::class);
        assert($logListener instanceof LogListener);

        $logListener->attach($eventManager, -2000);
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array<int, string>
     *
     * @throws void
     */
    public function getModuleDependencies(): array
    {
        return ['Laminas\Log'];
    }
}
