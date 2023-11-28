<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\File\Regex;
use CaptainHook\App\Hook\File\Regex\Aws;
use CaptainHook\App\Hook\File\Regex\Google;
use CaptainHook\App\Hook\File\Regex\Password;
use SebastianFeldmann\Git\Repository;

/**
 * Class BlockSecrets
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.19.0
 */
class BlockSecrets extends Check
{
    /**
     * List of user regex patterns collected from all configured providers
     *
     * @var array<string>
     */
    private array $blockedByProvider = [];

    /**
     * List of default regexes to block
     *
     * @var array<string>
     */
    private array $blockedByUser;

    /**
     * List of allowed patterns
     *
     * @var array<string>
     */
    private array $allowed;

    /**
     * Additional information for a file
     *
     * @var array<string, string>
     */
    private array $info = [];

    /**
     * Extract and validate config settings
     *
     * @param \CaptainHook\App\Config\Options $options
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function setUp(Config\Options $options): void
    {
        $this->setUpProvider($options);
        $this->blockedByUser = $options->get('blocked', []);
        $this->allowed       = $options->get('allowed', []);
    }

    /**
     * Tests if the given file doesn't contain invalid content
     *
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  string                            $file
     * @return bool
     */
    protected function isValid(Repository $repository, string $file): bool
    {
        $fileContent = (string) file_get_contents($file);
        $blocking    = $this->blockedByUser;
        if (!empty($this->blockedByProvider)) {
            $blocking = array_merge($blocking, $this->blockedByProvider);
        }
        foreach ($blocking as $regex) {
            $matchCount = (int)preg_match($regex, $fileContent, $matches);
            if ($matchCount && !$this->isAllowed($matches[0])) {
                $this->info[$file] = $matches[0];
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if a found blocked pattern should be allowed anyway
     *
     * @param  string $blocked
     * @return bool
     */
    private function isAllowed(string $blocked): bool
    {
        foreach ($this->allowed as $regex) {
            $matchCount = preg_match($regex, $blocked, $matches);
            if ($matchCount) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set up the blocked regex
     *
     * @param \CaptainHook\App\Config\Options $options
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    private function setUpProvider(Config\Options $options): void
    {
        $provider = $options->get('providers', []);
        foreach ($provider as $class) {
            if (!class_exists($class)) {
                throw new ActionFailed('Regex class ' . $class . ' not found');
            }
            /** @var Regex $reg */
            $reg = new $class();
            if (!$reg instanceof Regex) {
                throw new ActionFailed('Regex class ' . $class . ' is not implementing the correct interface');
            }
            $this->blockedByProvider = array_merge($this->blockedByProvider, $reg->patterns());
        }
    }

    /**
     * Return an error message appendix
     *
     * @param  string $file
     * @return string
     */
    protected function errorDetails(string $file): string
    {
        return ' found <comment>' . $this->info[$file] . '</comment>';
    }

    /**
     * Define the exception error message
     *
     * @param  int $filesFailed
     * @return string
     */
    protected function errorMessage(int $filesFailed): string
    {
        $s = $filesFailed > 1 ? 's' : '';
        return 'Found secrets in ' . $filesFailed . ' file' . $s;
    }
}
