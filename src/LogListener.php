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

namespace Mimmi20\ErrorHandling;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * a listener to log errors
 */
final class LogListener extends AbstractListenerAggregate
{
    /** @throws void */
    public function __construct(private readonly LoggerInterface $logger)
    {
        // nothing to do here
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'log'], $priority);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'log'], $priority);
    }

    /**
     * log mvc errors
     *
     * @throws void
     */
    public function log(MvcEvent $e): void
    {
        $exception = $e->getParam('exception');

        if (!$exception instanceof Throwable) {
            return;
        }

        $this->logger->error($exception);
    }
}
