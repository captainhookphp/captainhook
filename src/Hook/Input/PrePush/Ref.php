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
 * Git pre-push reference
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class Ref
{
    /**
     * Head path - refs/heads/main
     *
     * @var string
     */
    private $head;

    /**
     * Git hash
     *
     * @var string
     */
    private $hash;

    /**
     * Branch name
     *
     * @var string
     */
    private $branch;

    /**
     * Constructor
     *
     * @param string $head
     * @param string $hash
     * @param string $branch
     */
    public function __construct(string $head, string $hash, string $branch)
    {
        $this->head   = $head;
        $this->hash   = $hash;
        $this->branch = $branch;
    }

    /**
     * Head getter
     *
     * @return string
     */
    public function head(): string
    {
        return $this->head;
    }

    /**
     * Hash getter
     *
     * @return string
     */
    public function hash(): string
    {
        return $this->hash;
    }

    /**
     * Branch getter
     *
     * @return string
     */
    public function branch(): string
    {
        return $this->branch;
    }

    /**
     * Indicates if commit hash is a zero commit (0000000000000000000000000000000000000000)
     *
     * @return bool
     */
    public function isZeroHash(): bool
    {
        return (bool) preg_match('/^0+$/', $this->hash);
    }
}
