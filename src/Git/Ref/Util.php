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

/**
 * Util class
 *
 * Does some simple format and validation stuff
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
abstract class Util
{
    /**
     * Indicates if commit hash is a zero hash 0000000000000000000000000000000000000000
     *
     * @param  string $hash
     * @return bool
     */
    public static function isZeroHash(string $hash): bool
    {
        return (bool) preg_match('/^0+$/', $hash);
    }

    /**
     * Extract branch name from head path
     *
     *   refs/heads/main => main
     *
     * @param string $head
     * @return string
     */
    public static function extractBranchFromRefPath(string $head): string
    {
        $parts = explode('/', $head);
        return array_pop($parts);
    }
}
