<?php


declare(strict_types = 1);

namespace Mimmi20\ErrorHandling;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Log\Logger;
use Laminas\Mvc\MvcEvent;
use Throwable;

use function assert;

/**
 * a listener to log errors
 */
class LogListener extends AbstractListenerAggregate
{
    private Logger $logger;

    /**
     * @throws void
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param int $priority
     *
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH_ERROR,
            [$this, 'log'],
            $priority
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'log'],
            $priority
        );
    }

    /**
     * log mvc errors
     *
     * @throws void
     */
    public function log(MvcEvent $e): void
    {
        if (null === $e->getParam('exception')) {
            return;
        }

        $exception = $e->getParam('exception');
        assert($exception instanceof Throwable);

        $this->logger->err(
            $exception->getMessage(),
            ['Exception' => $exception]
        );
    }
}
