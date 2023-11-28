<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Regex;

use CaptainHook\App\Hook\File\Regex;

/**
 * Password regex
 *
 * Provides the regex to find generic passwords.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.19.0
 */
class Password implements Regex
{
    /**
     * Returns a list of patterns to check
     *
     * @return array<string>
     */
    public function patterns(): array
    {
        return [
            // Generic passwords
            '#password' . Util::OPTIONAL_QUOTE . Util::CONNECT . Util::OPTIONAL_QUOTE
            . '[a-z\\-_\\#/\\+0-9]{16,}' . Util::OPTIONAL_QUOTE . '#i',
        ];
    }
}
