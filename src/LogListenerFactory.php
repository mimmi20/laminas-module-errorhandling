<?php


declare(strict_types = 1);

namespace Mimmi20\ErrorHandling;

use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

/**
 * a factory to build the log listener
 */
class LogListenerFactory implements FactoryInterface
{
    /**
     * @param string       $requestedName
     * @param mixed[]|null $options
     * @phpstan-param array<mixed>|null $options
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): LogListener
    {
        $logger = $container->get(Logger::class);
        assert($logger instanceof Logger);

        return new LogListener($logger);
    }
}
