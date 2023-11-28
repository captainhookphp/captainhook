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

abstract class Util
{
    public const QUOTE          = '("|\')';
    public const OPTIONAL_QUOTE = self::QUOTE . '?';
    public const CONNECT        = '\s*(:|=>|=|:=)\s*';
}
