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
 * Google regex
 *
 * Provides the regex to find Google secrets.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.19.0
 */
class Google implements Regex
{
    /**
     * Returns a list of patterns to check
     *
     * @return array<string>
     */
    public function patterns(): array
    {
        return [
            // API Key
            '#' . Util::OPTIONAL_QUOTE . 'AIza[0-9A-Za-z-_]{35}' . Util::OPTIONAL_QUOTE . '#',
        ];
    }
}
