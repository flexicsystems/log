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

class Logger
{
    const NONE = 0;
    const DEBUG = 100;
    const INFO = 200;
    const NOTICE = 250;
    const WARNING = 300;
    const ERROR = 400;
    const CRITICAL = 500;
    const ALERT = 550;
    const EMERGENCY = 600;
    const EXCEPTION = 700;

    /**
     * @var array
     */
    public static $levels = array(
        self::NONE => 'NONE',
        self::DEBUG => 'DEBUG',
        self::INFO => 'INFO',
        self::NOTICE => 'NOTICE',
        self::WARNING => 'WARNING',
        self::ERROR => 'ERROR',
        self::CRITICAL => 'CRITICAL',
        self::EMERGENCY => 'EMERGENCY',
        self::EXCEPTION => 'EXCEPTION',
    );

    /**
     * @var int
     */
    public static $logLevel = self::DEBUG;

    /**
     * @var string
     */
    public static $format = '[#:LEVEL:#] #:MESSAGE:# { #:CONTEXT:# }';

    /**
     * @var string
     */
    public static $fallbackFile = '/var/log/log.log';

    /**
     * @var string
     */
    public static $contextSeparator = ' | ';

    /**
     * @var string
     */
    public static $timeFormat = 'Y-m-d H:i:s';

    /**
     * @var bool
     */
    public static $emptyLineAfter = false;

    /**
     * @var int
     */
    protected static $count = 0;

    /**
     * @param string $message
     * @param int    $level
     * @param string $file
     * @param array  $context
     *
     * @return bool
     */
    public static function log(string $message, int $level = self::DEBUG, string $file = '/var/log/log.log', array $context = array())
    {
        if ($level === self::NONE) {
            return false;
        }

        ++self::$count;

        if (!array_key_exists($level, self::$levels)) {
            $level = self::DEBUG;
        }

        $file = realpath($file);

        if ($file === false) {
            return false;
        }

        $file = is_file($file) ? $file : self::$fallbackFile;

        if (!is_file($file) || !is_readable($file) || !is_writable($file)) {
            return false;
        }

        $result = file_put_contents($file, self::getFormattedMessage($message, $level, implode(self::$contextSeparator, $context)) . PHP_EOL, FILE_APPEND);

        return $result !== false ? true : false;
    }

    /**
     * @param \Exception $exception
     * @param string     $file
     * @param array      $context
     *
     * @return bool
     */
    public static function logException(\Exception $exception, string $file = '/var/log/exceptions.log', array $context = array())
    {
        $file = realpath($file);

        if ($file === false) {
            return false;
        }

        $file = is_file($file) ? $file : self::$fallbackFile;

        if (!is_file($file) || !is_readable($file) || !is_writable($file)) {
            return false;
        }

        ++self::$count;

        $result = file_put_contents($file, self::getFormattedMessage($exception->getMessage(), self::EXCEPTION, implode(self::$contextSeparator, $context)) . PHP_EOL, FILE_APPEND);

        return $result !== false ? true : false;
    }

    /**
     * @return int
     */
    public static function getCount()
    {
        return self::$count;
    }

    /**
     * Format message.
     *
     * @param string $message
     * @param int    $level
     * @param string $context
     *
     * @return mixed
     */
    protected static function getFormattedMessage(string $message, int $level, string $context)
    {
        $result = str_replace('#:LEVEL:#', self::$levels[$level], self::$format);
        $result = str_replace('#:MESSAGE:#', $message, $result);
        $result = str_replace('#:CONTEXT:#', $context, $result);
        $result = str_replace('#:COUNT:#', (string) self::$count, $result);
        $result = str_replace('#:SESSION:#', session_id() === '' ? 'No Session ID' : session_id(), $result);
        $result = str_replace('#:TIME_INT:#', (string) time(), $result);
        $result = str_replace('#:TIME:#', (string) date(self::$timeFormat, time()), $result);

        return self::$emptyLineAfter ? $result . PHP_EOL : $result;
    }
}
