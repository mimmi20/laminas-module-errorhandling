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
final class LogListener extends AbstractListenerAggregate
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
        $exception = $e->getParam('exception');

        if (null === $exception) {
            return;
        }

        assert($exception instanceof Throwable);

        $this->logger->err(
            $exception->getMessage(),
            ['Exception' => $exception]
        );
    }
}
