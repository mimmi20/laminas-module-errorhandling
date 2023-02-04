<?php
/**
 * This file is part of the mimmi20/laminas-module-errorhandling package.
 *
 * Copyright (c) 2020-2021, Thomas Mueller <mimmi20@live.de>
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
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\IncompatibleReturnValueException;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class LogListenerTest extends TestCase
{
    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
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

        $callback = [$logListener, 'log'];

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager->expects(self::exactly(2))
            ->method('attach')
            ->willReturnMap(
                [
                    [MvcEvent::EVENT_DISPATCH_ERROR, $callback, $priority, new IsType(IsType::TYPE_CALLABLE)],
                    [MvcEvent::EVENT_RENDER_ERROR, $callback, $priority, new IsType(IsType::TYPE_CALLABLE)],
                ],
            );

        $logListener->attach($eventManager, $priority);
    }

    /**
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws IncompatibleReturnValueException
     * @throws InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
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

        $callback = [$logListener, 'log'];

        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager->expects(self::exactly(2))
            ->method('attach')
            ->willReturnMap(
                [
                    [MvcEvent::EVENT_DISPATCH_ERROR, $callback, $priority, new IsType(IsType::TYPE_CALLABLE)],
                    [MvcEvent::EVENT_RENDER_ERROR, $callback, $priority, new IsType(IsType::TYPE_CALLABLE)],
                ],
            );

        $logListener->attach($eventManager);
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
