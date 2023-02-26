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

use CaptainHook\App\Git;

class Range implements Git\Range
{
    /**
     * @var \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    private $from;

    /**
     * @var \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    private $to;

    /**
     * Constructor
     *
     * @param \CaptainHook\App\Hook\Input\PrePush\Ref $from
     * @param \CaptainHook\App\Hook\Input\PrePush\Ref $to
     */
    public function __construct(Ref $from, Ref $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * Returns the start ref
     *
     * @return \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    public function from(): Ref
    {
        return $this->from;
    }

    /**
     * Returns the end ref
     *
     * @return \CaptainHook\App\Hook\Input\PrePush\Ref
     */
    public function to(): Ref
    {
        return $this->to;
    }
}
