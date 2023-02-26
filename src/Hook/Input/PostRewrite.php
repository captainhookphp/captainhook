<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Input;

use CaptainHook\App\Console\IO;
use CaptainHook\App\Git\Range;
use CaptainHook\App\Git\Ref;

/**
 * Class to access the pre-push stdIn data
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class PostRewrite implements Range\Detecting
{
    /**
     * Returns list of refs
     *
     * @param  \CaptainHook\App\Console\IO $io
     * @return \CaptainHook\App\Git\Range[]
     */
    public function getRanges(IO $io): array
    {
        return $this->createFromStdIn($io->getStandardInput());
    }

    /**
     * Create ranges from stdIn
     *
     * @param  array<string> $stdIn
     * @return array<\CaptainHook\App\Git\Range>
     */
    private function createFromStdIn(array $stdIn): array
    {
        $ranges = [];
        foreach ($stdIn as $line) {
            if (!empty($line)) {
                $parts    = explode(' ', trim($line));
                $from     = new Ref\Generic(!empty($parts[1]) ? $parts[1] . '^' : 'HEAD@{1}');
                $to       = new Ref\Generic('HEAD');
                $ranges[] = new Range\Generic($from, $to);
            }
        }
        return $ranges;
    }
}
