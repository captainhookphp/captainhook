<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command;

use CaptainHook\App\Config;
use SebastianFeldmann\Git\Repository;

/**
 * Class Formatter
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
class Formatter
{
    /**
     * Cache storage for computed placeholder values
     *
     * @var array<string, string>
     */
    private static $cache = [];

    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    private $config;

    /**
     * List of available placeholders
     *
     * @var array<string, string>
     */
    private static $placeholders = [
        'config'       => '\\CaptainHook\\App\\Runner\\Action\\Cli\\Command\\Placeholder\\Config',
        'env'          => '\\CaptainHook\\App\\Runner\\Action\\Cli\\Command\\Placeholder\\Env',
        'staged_files' => '\\CaptainHook\\App\\Runner\\Action\\Cli\\Command\\Placeholder\\StagedFiles'
    ];

    /**
     * Git repository
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    private $repository;

    /**
     * Original hook arguments
     *
     * @var array<string, string>
     */
    private $arguments;

    /**
     * Formatter constructor
     *
     * @param \SebastianFeldmann\Git\Repository $repository
     * @param array<string, string>             $arguments
     */
    public function __construct(Config $config, Repository $repository, array $arguments)
    {
        $this->config     = $config;
        $this->repository = $repository;
        $this->arguments  = $arguments;
    }

    /**
     * Replaces all placeholders in a cli command
     *
     * @param  string $command
     * @return string
     */
    public function format(string $command): string
    {
        // find all replacements {SOMETHING}
        $placeholders = $this->findAllPlaceholders($command);
        foreach ($placeholders as $placeholder) {
            $command = str_replace('{$' . $placeholder . '}', $this->replace($placeholder), $command);
        }

        return $command;
    }

    /**
     * Returns al list of all placeholders
     *
     * @param  string $command
     * @return array<int, string>
     */
    private function findAllPlaceholders(string $command): array
    {
        $placeholders = [];
        $matches      = [];

        if (preg_match_all('#{\$([a-z_]+(\|[a-z\-]+:.*)?)}#iU', $command, $matches)) {
            foreach ($matches[1] as $match) {
                $placeholders[] = $match;
            }
        }

        return $placeholders;
    }

    /**
     * Return a given placeholder value
     *
     * @param  string $placeholder
     * @return string
     */
    private function replace(string $placeholder): string
    {
        // if placeholder references an original hook argument return the argument
        // otherwise compute the placeholder
        return $this->arguments[strtolower($placeholder)] ?? $this->computedPlaceholder($placeholder);
    }

    /**
     * Compute the placeholder value
     *
     * @param  string $rawPlaceholder Placeholder syntax {$NAME[|OPTION:VALUE]...}
     * @return string
     */
    private function computedPlaceholder(string $rawPlaceholder): string
    {
        // to not compute the same placeholder multiple times
        if (!$this->isCached($rawPlaceholder)) {
            // extract placeholder name and options
            $parts       = explode('|', $rawPlaceholder);
            $placeholder = strtolower($parts[0]);
            $options     = $this->parseOptions(array_slice($parts, 1));

            if (!$this->isPlaceholderValid($placeholder)) {
                return '';
            }

            $processor = $this->createPlaceholder($placeholder);
            $this->cache($rawPlaceholder, $processor->replacement($options));
        }
        return $this->cached($rawPlaceholder);
    }

    /**
     * Placeholder factory method
     *
     * @param  string $placeholder
     * @return \CaptainHook\App\Runner\Action\Cli\Command\Placeholder
     */
    private function createPlaceholder(string $placeholder): Placeholder
    {
        /** @var class-string<\CaptainHook\App\Runner\Action\Cli\Command\Placeholder> $class */
        $class = self::$placeholders[$placeholder];
        return new $class($this->config, $this->repository);
    }

    /**
     * Checks if a placeholder is available for computation
     *
     * @param  string $placeholder
     * @return bool
     */
    private function isPlaceholderValid(string $placeholder): bool
    {
        return isset(self::$placeholders[$placeholder]);
    }

    /**
     * Parse options from ["name:'value'", "name:'value'"] to ["name" => "value", "name" => "value"]
     *
     * @param  array<int, string> $raw
     * @return array<string, string>
     */
    private function parseOptions(array $raw): array
    {
        $options = [];
        foreach ($raw as $rawOption) {
            $matches = [];
            if (preg_match('#^([a-z_\-]+):(.*)?$#i', $rawOption, $matches)) {
                $options[strtolower($matches[1])] = $matches[2];
            }
        }
        return $options;
    }

    /**
     * Check if a placeholder is cached already
     *
     * @param  string $placeholder
     * @return bool
     */
    private static function isCached(string $placeholder): bool
    {
        return isset(self::$cache[$placeholder]);
    }

    /**
     * Cache a given placeholder value
     *
     * @param string $placeholder
     * @param string $replacement
     */
    private static function cache(string $placeholder, string $replacement): void
    {
        self::$cache[$placeholder] = $replacement;
    }

    /**
     * Return cached value for given placeholder
     *
     * @param  string $placeholder
     * @return string
     */
    private static function cached(string $placeholder): string
    {
        return self::$cache[$placeholder] ?? '';
    }
}
