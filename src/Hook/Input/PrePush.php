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
use CaptainHook\App\Git\Range\Detecting;
use CaptainHook\App\Git\Ref\Util;
use CaptainHook\App\Hook\Input\PrePush\Range;
use CaptainHook\App\Hook\Input\PrePush\Ref;

/**
 * Class to access the pre-push stdIn data
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class PrePush implements Detecting
{
    /**
     * Returns list of refs
     *
     * @param  \CaptainHook\App\Console\IO $io
     * @return array<\CaptainHook\App\Hook\Input\PrePush\Range>
     */
    public function getRanges(IO $io): array
    {
        return $this->createFromStdIn($io->getStandardInput());
    }

    /**
     * Factory method
     *
     * @param  array<string> $stdIn
     * @return array<\CaptainHook\App\Hook\Input\PrePush\Range>
     */
    private function createFromStdIn(array $stdIn): array
    {
        $ranges = [];
        foreach ($stdIn as $line) {
            if (empty($line)) {
                continue;
            }

            [$localRef, $localHash, $remoteRef, $remoteHash] = explode(' ', trim($line));

            if (Util::isZeroHash($remoteHash)) {
                continue;
            }

            $from     = new Ref($remoteRef, $remoteHash, Util::extractBranchFromRefPath($remoteRef));
            $to       = new Ref($localRef, $localHash, Util::extractBranchFromRefPath($localRef));
            $ranges[] = new Range($from, $to);
        }
        return $ranges;
    }
}
