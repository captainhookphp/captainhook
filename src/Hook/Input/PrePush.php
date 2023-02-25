<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Input;

use CaptainHook\App\Hook\Input\PrePush\RefInfo;

/**
 * Class to access the pre-push stdIn data
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class PrePush
{
    /**
     * List of refs to push
     *
     * @var array<RefInfo>
     */
    private $refs;

    /**
     * Constructor
     *
     * @param array<RefInfo> $refs
     */
    public function __construct(array $refs)
    {
        $this->refs = $refs;
    }

    /**
     * Returns list of refs
     *
     * @return \CaptainHook\App\Hook\Input\PrePush\RefInfo[]
     */
    public function all(): array
    {
        return $this->refs;
    }

    /**
     * Factory method
     *
     * @param  array<string> $stdIn
     * @return PrePush
     */
    public static function createFromStdIn(array $stdIn): self
    {
        $refs = [];
        foreach ($stdIn as $line) {
            if (!empty($line)) {
                $refs[] = RefInfo::fromGitPushInfoLine($line);
            }
        }
        return new self($refs);
    }
}
