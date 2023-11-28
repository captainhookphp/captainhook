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
 * GitHub regex
 *
 * Provides the regex to find GitHub secrets.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.19.0
 */
class GitHub implements Regex
{
    /**
     * Returns a list of patterns to check
     *
     * @return array<string>
     */
    public function patterns(): array
    {
        return [
            // Personal Access Token (Classic)
            '#' . Util::OPTIONAL_QUOTE . 'ghp_[a-zA-Z0-9]{36}' . Util::OPTIONAL_QUOTE . '#',

            // Personal Access Token (Fine-Grained)
            '#' . Util::OPTIONAL_QUOTE . 'github_pat_[a-zA-Z0-9]{22}_[a-zA-Z0-9]{59}' . Util::OPTIONAL_QUOTE . '#',

            // User-To-Server Access Token
            '#' . Util::OPTIONAL_QUOTE . 'ghu_[a-zA-Z0-9]{36}' . Util::OPTIONAL_QUOTE . '#',

            // Server-To-Server Access Token
            '#' . Util::OPTIONAL_QUOTE . 'ghs_[a-zA-Z0-9]{36}' . Util::OPTIONAL_QUOTE . '#',
        ];
    }
}
