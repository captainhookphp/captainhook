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
 * Aws regex
 *
 * Provides the regex to find AWS secrets.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.19.0
 */
class Aws implements Regex
{
    /**
     * Possible AWS pattern
     */
    public const AWS = '(AWS|aws|Aws)?_?';

    /**
     * Returns a list of patterns to check
     *
     * @return array<string>
     */
    public function patterns(): array
    {
        return [
            // AWS token
            '#(A3T[A-Z0-9]|AKIA|AGPA|AIDA|AROA|AIPA|ANPA|ANVA|ASIA)[A-Z0-9]{16}#',

            // AWS secrets, keys, access token
            '#' . Util::OPTIONAL_QUOTE . self::AWS . '(SECRET|secret|Secret)?_?(ACCESS|access|Access)?_?(KEY|key|Key)'
            . Util::OPTIONAL_QUOTE . Util::CONNECT
            . Util::OPTIONAL_QUOTE . '[A-Za-z0-9/\\+=]{40}' . Util::OPTIONAL_QUOTE . '#',

            // AWS account id
            '#' . Util::OPTIONAL_QUOTE . self::AWS . '(ACCOUNT|account|Account)_?(ID|id|Id)?' . Util::OPTIONAL_QUOTE
            . Util::CONNECT . Util::OPTIONAL_QUOTE . '[0-9]{4}\\-?[0-9]{4}\\-?[0-9]{4}' . Util::OPTIONAL_QUOTE . '#',
        ];
    }
}
