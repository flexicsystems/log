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

use Flexic\Log\Exceptions\LogicException;
use Flexic\Log\Interfaces\LoggerInterface;

class StaticLogger
{
    protected static $logger;

    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function emergency(string $message, array $context): void
    {
        self::log(LogLevel::EMERGENCY, $message, $context);
    }

    public static function alert(string $message, array $context): void
    {
        self::log(LogLevel::ALERT, $message, $context);
    }

    public static function critical(string $message, array $context): void
    {
        self::log(LogLevel::CRITICAL, $message, $context);
    }

    public static function error(string $message, array $context): void
    {
        self::log(LogLevel::ERROR, $message, $context);
    }

    public static function warning(string $message, array $context): void
    {
        self::log(LogLevel::WARNING, $message, $context);
    }

    public static function notice(string $message, array $context): void
    {
        self::log(LogLevel::NOTICE, $message, $context);
    }

    public static function info(string $message, array $context): void
    {
        self::log(LogLevel::INFO, $message, $context);
    }

    public static function debug(string $message, array $context): void
    {
        self::log(LogLevel::DEBUG, $message, $context);
    }

    public static function log(string $level, string $message, array $context): void
    {
        if (self::$logger === null) {
            throw new LogicException('Static logger is not defined.');
        }

        self::$logger->log($level, $message, $context);
    }

    public static function exception(\Exception $exception): void
    {
        if (self::$logger === null) {
            throw new LogicException('Static logger is not defined.');
        }

        self::$logger->exception($exception);
    }
}
