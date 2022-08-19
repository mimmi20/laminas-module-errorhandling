<?php


namespace Mimmi20Test\ErrorHandling;

use Laminas\Log\Logger;
use Mimmi20\ErrorHandling\LogListener;
use Mimmi20\ErrorHandling\LogListenerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LogListenerFactoryTest extends TestCase
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
        $container->expects(static::once())
            ->method('get')
            ->with(Logger::class)
            ->willReturn($logger);
        $container->expects(static::never())
            ->method('has');

        $result = $this->object->__invoke($container, '');

        static::assertInstanceOf(LogListener::class, $result);
    }
}
