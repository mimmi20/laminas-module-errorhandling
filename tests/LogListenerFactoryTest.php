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

use AssertionError;
use Laminas\Log\Logger;
use Mimmi20\ErrorHandling\LogListener;
use Mimmi20\ErrorHandling\LogListenerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class LogListenerFactoryTest extends TestCase
{
    private LogListenerFactory $object;

    /**
     * @throws void
     */
    protected function setUp(): void
    {
        $this->object = new LogListenerFactory();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvoke(): void
    {
        $logger = $this->createMock(Logger::class);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(Logger::class)
            ->willReturn($logger);
        $container->expects(self::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        self::assertInstanceOf(LogListener::class, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvoke2(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(Logger::class)
            ->willReturn(null);
        $container->expects(self::never())
            ->method('has');

        $this->expectException(AssertionError::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('assert($logger instanceof Logger)');

        $this->object->__invoke($container, '');
    }
}
