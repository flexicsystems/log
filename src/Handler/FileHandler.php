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

namespace Flexic\Log\Handler;

class FileHandler
{
    private $file;

    private $format;

    public function __construct(string $file, string $format = '[%timestamp|Y-m-d H:i:s%] %channel|lower%.%level%: %message%' . PHP_EOL)
    {
        if (!is_file($file) && !is_dir($file)) {
            file_put_contents($file, '<------ this file was automatically created ------>' . PHP_EOL);
        }

        if (is_file($file) && !is_dir($file) && (!is_writable($file) || !is_readable($file))) {
            throw new \Exception(sprintf('File %s does not exist or is not accessible', $file));
        }

        $this->file = $file;
        $this->format = $format;
    }

    public function handle(array $request): void
    {
        $message = preg_replace('/\{[\s]?([a-zA-Z0-9]*)[\s]?}/', '{$1}', $request['message']);

        if (!\is_string($message)) {
            throw new \Exception(sprintf('Could not normalize placeholder in message %s', $request['message']));
        }

        foreach ($request['context'] as $key => $data) {
            $data = $this->format($data);

            $message = str_replace(sprintf('{%s}', $key), $data, $message);
        }

        $request['message'] = $message;

        file_put_contents($this->file, $this->formatMessage($request), FILE_APPEND);
    }

    private function format($data): string
    {
        if ($data === null || \is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string) $data;
        }

        if ($data instanceof \Exception) {
            return $data->getMessage();
        }

        return @$this->toJson($data);
    }

    private function toJson($data): string
    {
        $result = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);

        if (!\is_string($result)) {
            throw new \Exception('Could not convert data to json');
        }

        return $result;
    }

    private function formatMessage(array $request): string
    {
        $result = $this->format;

        // Timestamp
        $result = str_replace('%timestamp%', $request['timestamp'], $result);
        if ($this->checkMessagePattern($result, '%timestamp')) {
            preg_match_all('/(%timestamp(|([^%]+))%)/', $this->validateMessage($result), $patterns);

            foreach ($patterns[0] as $key => $pattern) {
                if ($pattern !== '%timestamp%') {
                    $result = str_replace($pattern, date(substr($patterns[2][$key], 1), $request['timestamp']), $result);
                }
            }
        }

        // Channel
        $result = str_replace('%channel%', $request['channel'], $result);
        if ($this->checkMessagePattern($result, '%channel')) {
            preg_match_all('/(%channel(|([^%]+))%)/', $this->validateMessage($result), $patterns);

            foreach ($patterns[2] as $key => $pattern) {
                switch (strtolower(substr($pattern, 1))) {
                    case 'lower':
                        $result = str_replace($patterns[0][$key], strtolower($request['channel']), $result);
                        break;
                    case 'upper':
                        $result = str_replace($patterns[0][$key], strtoupper($request['channel']), $result);
                }
            }
        }

        // Level
        $result = str_replace('%level%', $request['level'], $result);

        // Message
        $result = str_replace('%message%', $request['message'], $result);
        if ($this->checkMessagePattern($result, '%message')) {
            preg_match_all('/(%message(|([^%]+))%)/', $this->validateMessage($result), $patterns);

            foreach ($patterns[2] as $key => $pattern) {
                switch (strtolower(substr($pattern, 1))) {
                    case 'lower':
                        $result = str_replace($patterns[0][$key], strtolower($request['message']), $result);
                        break;
                    case 'upper':
                        $result = str_replace($patterns[0][$key], strtoupper($request['message']), $result);
                }
            }
        }

        return $this->validateMessage($result);
    }

    private function checkMessagePattern($message, string $pattern): bool
    {
        if (!\is_string($message)) {
            throw new \Exception('Failed while formatting message');
        }

        if (strpos($message, $pattern) === false) {
            return false;
        }

        return true;
    }

    private function validateMessage($message): string
    {
        if (!\is_string($message)) {
            throw new \Exception('Failed while formatting message');
        }

        return $message;
    }
}
