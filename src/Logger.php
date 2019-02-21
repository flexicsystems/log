<?php
/**
 * Flexic
 * Copyright (c) 2019 ThemePoint
 *
 * @author Hendrik Legge <hendrik.legge@themepoint.de>
 * @version 1.0.0
 * @package flexic.component
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Flexic\Log;

use Flexic\Log\Exceptions\InvalidArgumentException;
use Flexic\Log\Exceptions\LevelException;
use Flexic\Log\Exceptions\LogicException;
use Flexic\Log\Interfaces\HandlerInterface;
use Flexic\Log\Interfaces\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @var array
     */
    private $handlers;

    /**
     * @var array
     */
    private $processors;

    public function __construct(string $channel, array $handlers = array(), array $processors = array())
    {
        $this->channel = $channel;
        $this->handlers = $handlers;
        $this->processors = $processors;
    }

    public function pushHandler(HandlerInterface $handler): self
    {
        array_unshift($this->handlers, $handler);

        return $this;
    }

    public function popHandler(): HandlerInterface
    {
        if (\count($this->handlers) === 0) {
            throw new LogicException('Could not pop from empty handler stack');
        }

        return array_shift($this->handlers);
    }

    public function pushProcessor(callable $handler): self
    {
        array_unshift($this->processors, $handler);

        return $this;
    }

    public function popProcessor(): callable
    {
        if (\count($this->handlers) === 0) {
            throw new LogicException('Could not pop from empty processor stack');
        }

        return array_shift($this->processors);
    }

    public function emergency(string $message, array $context): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert(string $message, array $context): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string $message, array $context): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string $message, array $context): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string $message, array $context): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string $message, array $context): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string $message, array $context): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string $message, array $context): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function exception(\Exception $exception): void
    {
        $this->log(LogLevel::ERROR, $exception->getMessage(), array());
    }

    public function log(string $level, string $message, array $context): void
    {
        if (!\in_array($level, LogLevel::$levels)) {
            throw new LevelException(sprintf('Debug level %s is not defined', $level));
        }

        preg_match_all('/\{[\s]?([a-zA-Z0-9]*)[\s]?}/', $message, $placeholders);
        $keys = array_keys($context);

        if (!\is_array($placeholders) || !\is_array($keys)) {
            throw new LogicException('Could not compare placeholders and context keys.');
        }

        if (sort($keys) !== sort($placeholders)) {
            throw new InvalidArgumentException('Placeholders in messages are not equal with context keys.');
        }

        $request = array(
            'message' => $message,
            'context' => $context,
            'level' => $level,
            'channel' => $this->channel,
            'timestamp' => time(),
        );

        foreach ($this->processors as $processor) {
            $request = \call_user_func($processor, $request);
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($request);
        }
    }
}
