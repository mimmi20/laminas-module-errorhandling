<?php
/**
 * This file is part of the mimmi20/laminas-module-errorhandling package.
 *
 * Copyright (c) 2020-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\ErrorHandling;

use AssertionError;
use Laminas\EventManager\EventInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mimmi20\ErrorHandling\LogListener;
use Mimmi20\ErrorHandling\Module;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ModuleTest extends TestCase
{
    /** @throws Exception */
    public function testGetConfig(): void
    {
        $object = new Module();
        $config = $object->getConfig();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('service_manager', $config);

        self::assertIsArray($config['service_manager']);
        self::assertCount(1, $config['service_manager']);
        self::assertArrayHasKey('factories', $config['service_manager']);

        self::assertIsArray($config['service_manager']['factories']);
        self::assertCount(1, $config['service_manager']['factories']);
        self::assertArrayHasKey(LogListener::class, $config['service_manager']['factories']);
    }

    /** @throws Exception */
    public function testGetServiceConfig(): void
    {
        $object = new Module();
        $config = $object->getServiceConfig();

        self::assertIsArray($config);
        self::assertCount(1, $config);
        self::assertArrayHasKey('factories', $config);

        self::assertIsArray($config['factories']);
        self::assertCount(1, $config['factories']);
        self::assertArrayHasKey(LogListener::class, $config['factories']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testOnBootstrap(): void
    {
        $event = $this->getMockBuilder(EventInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');

        $object = new Module();
        $object->onBootstrap($event);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testOnBootstrap2(): void
    {
        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::once())
            ->method('getApplication')
            ->willReturn(null);

        $object = new Module();

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($application instanceof ApplicationInterface)');

        $object->onBootstrap($event);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testOnBootstrap3(): void
    {
        $application = $this->getMockBuilder(ApplicationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $application->expects(self::once())
            ->method('getEventManager')
            ->willReturn(null);
        $application->expects(self::never())
            ->method('getServiceManager');

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::once())
            ->method('getApplication')
            ->willReturn($application);

        $object = new Module();

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($eventManager instanceof EventManagerInterface)');

        $object->onBootstrap($event);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testOnBootstrap4(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $application = $this->getMockBuilder(ApplicationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $application->expects(self::once())
            ->method('getEventManager')
            ->willReturn($eventManager);
        $application->expects(self::once())
            ->method('getServiceManager')
            ->willReturn(null);

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::once())
            ->method('getApplication')
            ->willReturn($application);

        $object = new Module();

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($serviceManager instanceof ServiceLocatorInterface)');

        $object->onBootstrap($event);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testOnBootstrap5(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $serviceManager = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->expects(self::once())
            ->method('get')
            ->with(LogListener::class)
            ->willReturn(null);
        $serviceManager->expects(self::never())
            ->method('has');

        $application = $this->getMockBuilder(ApplicationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $application->expects(self::once())
            ->method('getEventManager')
            ->willReturn($eventManager);
        $application->expects(self::once())
            ->method('getServiceManager')
            ->willReturn($serviceManager);

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::once())
            ->method('getApplication')
            ->willReturn($application);

        $object = new Module();

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($logListener instanceof ListenerAggregateInterface)');

        $object->onBootstrap($event);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function testOnBootstrap6(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $logListener = $this->getMockBuilder(ListenerAggregateInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logListener->expects(self::once())
            ->method('attach')
            ->with($eventManager, -2000);
        $logListener->expects(self::never())
            ->method('detach');

        $serviceManager = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $serviceManager->expects(self::once())
            ->method('get')
            ->with(LogListener::class)
            ->willReturn($logListener);
        $serviceManager->expects(self::never())
            ->method('has');

        $application = $this->getMockBuilder(ApplicationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $application->expects(self::once())
            ->method('getEventManager')
            ->willReturn($eventManager);
        $application->expects(self::once())
            ->method('getServiceManager')
            ->willReturn($serviceManager);

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::once())
            ->method('getApplication')
            ->willReturn($application);

        $object = new Module();

        $object->onBootstrap($event);
    }
}
