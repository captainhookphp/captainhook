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
     * Should the default block regexes be used
     *
     * @var bool
     */
    private bool $blockDefault;

    /**
     * List of default regexes to block
     *
     * @var array<string>
     */
    private array $blockedByDefault;

    /**
     * List of user defined regex patterns
     *
     * @var array<string>
     */
    private array $blockedByUser;

    /**
     * List of allowed patterns
     *
     * @var array<string>
     */
    private array $allowedByUser;

    /**
     * Additional information for a file
     *
     * @var array<string, string>
     */
    private array $info = [];

    /**
     * Extract and validate config settings
     *
     * @param  \CaptainHook\App\Config\Options $options
     */
    protected function setUp(Config\Options $options): void
    {
        $this->setUpDefaultBlocks();
        $this->blockDefault  = $options->get('blockDefaults', true);
        $this->blockedByUser = $options->get('blocked', []);
        $this->allowedByUser = $options->get('allowed', []);
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
        $matchCount  = 0;
        $fileContent = (string) file_get_contents($file);
        $blocked     = $this->blockedByUser;
        if ($this->blockDefault) {
            $blocked = array_merge($blocked, $this->blockedByDefault);
        }
        foreach ($blocked as $regex) {
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
        foreach ($this->allowedByUser as $regex) {
            $matchCount = preg_match($regex, $blocked, $matches);
            if ($matchCount) {
                return true;
            }
        }
        return false;
    }

    private function setUpDefaultBlocks(): void
    {
        $aws      = '(AWS|aws|Aws)?_?';
        $quote    = '("|\')';
        $optQuote = $quote . '?';
        $connect  = '\s*(:|=>|=|:=)\s*';

        $this->blockedByDefault = [
            // AWS token
            '#(A3T[A-Z0-9]|AKIA|AGPA|AIDA|AROA|AIPA|ANPA|ANVA|ASIA)[A-Z0-9]{16}#',
            // AWS secrets, keys, access token
            '#' . $optQuote . $aws . '(SECRET|secret|Secret)?_?(ACCESS|access|Access)?_?(KEY|key|Key)' . $optQuote
            . $connect . $optQuote . '[A-Za-z0-9/\\+=]{40}' . $optQuote . '#',
            // AWS account id
            '#' . $optQuote . $aws . '(ACCOUNT|account|Account)_?(ID|id|Id)?' . $optQuote
            . $connect . $optQuote . '[0-9]{4}\\-?[0-9]{4}\\-?[0-9]{4}' . $optQuote . '#',
            // try to find any password
            '#password' . $optQuote . $connect . $optQuote . '[a-z\\-_\\#/\\+0-9]{16,}' . $optQuote . '#i',
        ];
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
