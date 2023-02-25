<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Input\PrePush;

/**
 * Represents a std input line from a pre-push hook
 *
 * refs/heads/main 50069efa632090299636a136d7aea150aa64bae4 refs/heads/main 8309f6e16097754469c485e604900c573bf2c5d8
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class RefInfo
{
    /**
     * Local ref
     *
     * @var \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    private $local;

    /**
     * Remote ref
     *
     * @var \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    private $remote;

    /**
     * Constructor
     *
     * @param string $localRef
     * @param string $localHash
     * @param string $remoteRef
     * @param string $remoteHash
     */
    public function __construct(string $localRef, string $localHash, string $remoteRef, string $remoteHash)
    {
        $this->local  = new Ref($localRef, $localHash, $this->extractBranch($localRef));
        $this->remote = new Ref($remoteRef, $remoteHash, $this->extractBranch($remoteRef));
    }

    /**
     * Local head getter
     *
     * @return \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    public function local(): Ref
    {
        return $this->local;
    }

    /**
     * Local hash getter
     *
     * @return \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    public function remote(): Ref
    {
        return $this->remote;
    }

    /**
     * Extract branch name from head path
     *
     *   refs/heads/main => main
     *
     * @param string $head
     * @return string
     */
    private function extractBranch(string $head): string
    {
        $parts = explode('/', $head);
        return array_pop($parts);
    }

    /**
     * @param  string $line
     * @return RefInfo
     */
    public static function fromGitPushInfoLine(string $line): self
    {
        [$localRef, $localHash, $remoteRef, $remoteHash] = explode(' ', $line);

        return new self(trim($localRef), trim($localHash), trim($remoteRef), trim($remoteHash));
    }
}
