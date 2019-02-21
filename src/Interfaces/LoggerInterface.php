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

namespace Flexic\Log\Interfaces;

interface LoggerInterface
{
    public function emergency(string $message, array $context): void;

    public function alert(string $message, array $context): void;

    public function critical(string $message, array $context): void;

    public function error(string $message, array $context): void;

    public function warning(string $message, array $context): void;

    public function notice(string $message, array $context): void;

    public function info(string $message, array $context): void;

    public function debug(string $message, array $context): void;

    public function log(string $level, string $message, array $context): void;
}
