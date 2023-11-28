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
 * Stripe regex
 *
 * Provides the regex to find Stripe secrets.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.19.0
 */
class Stripe implements Regex
{
    /**
     * Returns a list of patterns to check
     *
     * @return array<string>
     */
    public function patterns(): array
    {
        return [
            // Standard API Key & Restricted API Key
            '#' . Util::OPTIONAL_QUOTE . 'sk_live_[0-9a-z]{24}' . Util::OPTIONAL_QUOTE . '#',
        ];
    }
}
