<?php

/**
 * This file is part of the mimmi20/laminas-module-errorhandling package.
 *
 * Copyright (c) 2020-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20Test\ErrorHandling;

use Exception;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Mimmi20\ErrorHandling\LogListener;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionException;
use ReflectionProperty;

final class LogListenerTest extends TestCase
{
    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws ReflectionException
     */
    public function testAttach(): void
    {
        $priority = 4711;

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('error');

        $logListener = new LogListener($logger);

        $callback1 = [$logListener, 'log1'];
        $callback2 = [$logListener, 'log2'];

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager->expects(self::exactly(2))
            ->method('attach')
            ->willReturnMap(
                [
                    [MvcEvent::EVENT_DISPATCH_ERROR, [$logListener, 'log'], $priority, $callback1],
                    [MvcEvent::EVENT_RENDER_ERROR, [$logListener, 'log'], $priority, $callback2],
                ],
            );

        $logListener->attach($eventManager, $priority);

        $property      = new ReflectionProperty($logListener, 'listeners');
        $propertyValue = $property->getValue($logListener);

        self::assertIsArray($propertyValue);
        self::assertCount(2, $propertyValue);
        self::assertSame($callback1, $propertyValue[0]);
        self::assertSame($callback2, $propertyValue[1]);
    }

    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws ReflectionException
     */
    public function testAttach2(): void
    {
        $priority = 1;

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('error');

        $logListener = new LogListener($logger);

        $callback1 = [$logListener, 'log1'];
        $callback2 = [$logListener, 'log2'];

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager->expects(self::exactly(2))
            ->method('attach')
            ->willReturnMap(
                [
                    [MvcEvent::EVENT_DISPATCH_ERROR, [$logListener, 'log'], $priority, $callback1],
                    [MvcEvent::EVENT_RENDER_ERROR, [$logListener, 'log'], $priority, $callback2],
                ],
            );

        $logListener->attach($eventManager);

        $property      = new ReflectionProperty($logListener, 'listeners');
        $propertyValue = $property->getValue($logListener);

        self::assertIsArray($propertyValue);
        self::assertCount(2, $propertyValue);
        self::assertSame($callback1, $propertyValue[0]);
        self::assertSame($callback2, $propertyValue[1]);
    }

    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws ReflectionException
     */
    public function testAttach3(): void
    {
        $priority = 2;

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('error');

        $logListener = new LogListener($logger);

        $callback1 = [$logListener, 'log1'];
        $callback2 = [$logListener, 'log2'];

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager->expects(self::exactly(2))
            ->method('attach')
            ->willReturnMap(
                [
                    [MvcEvent::EVENT_DISPATCH_ERROR, [$logListener, 'log'], $priority, $callback1],
                    [MvcEvent::EVENT_RENDER_ERROR, [$logListener, 'log'], $priority, $callback2],
                ],
            );

        $logListener->attach($eventManager);

        $property      = new ReflectionProperty($logListener, 'listeners');
        $propertyValue = $property->getValue($logListener);

        self::assertIsArray($propertyValue);
        self::assertCount(2, $propertyValue);
        self::assertNull($propertyValue[0]);
        self::assertNull($propertyValue[1]);
    }

    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testLog(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::never())
            ->method('error');

        $logListener = new LogListener($logger);

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::never())
            ->method('getApplication');
        $event->expects(self::once())
            ->method('getParam')
            ->with('exception')
            ->willReturn(null);

        $logListener->log($event);
    }

    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testLog2(): void
    {
        $message   = 'test-error-message';
        $exception = new Exception($message);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger->expects(self::once())
            ->method('error')
            ->with($exception);

        $logListener = new LogListener($logger);

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects(self::never())
            ->method('getName');
        $event->expects(self::never())
            ->method('getApplication');
        $event->expects(self::once())
            ->method('getParam')
            ->with('exception')
            ->willReturn($exception);

        $logListener->log($event);
    }
}
