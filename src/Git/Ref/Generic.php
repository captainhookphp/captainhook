<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Git\Ref;

use CaptainHook\App\Git\Ref;

/**
 * Generic range implementation
 *
 * The simplest imaginable range implementation without any extra information.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class Generic implements Ref
{
    /**
     * Referencing a git state
     *
     * @var string
     */
    private string $id;

    /**
     * Constructor
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Return the git reference
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
}
