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
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

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
    private Repository $repository;

    private string $hashOfBranchOrigin;

    /**
     * Returns list of refs
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \SebastianFeldmann\Git\Repository $repository
     *
     * @return array<\CaptainHook\App\Hook\Input\PrePush\Range>
     */
    public function getRanges(IO $io, Repository $repository): array
    {
        $this->repository = $repository;

        return $this->createFromStdIn($io->getStandardInput());
    }

    /**
     * Factory method
     *
     * @param array<string> $stdIn
     *
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
                $remoteHash = $this->getHashOfBranchOrigin();

                if (Util::isZeroHash($remoteHash)) {
                    continue;
                }
            }

            $from = new Ref($remoteRef, $remoteHash, Util::extractBranchFromRefPath($remoteRef));
            $to = new Ref($localRef, $localHash, Util::extractBranchFromRefPath($localRef));
            $ranges[] = new Range($from, $to);
        }

        return $ranges;
    }

    private function getHashOfBranchOrigin(): string
    {
        if (!isset($this->hashOfBranchOrigin)) {
            $currentBranch = $this->repository->getInfoOperator()
                                              ->getCurrentBranch();

            $processor = new Processor();
            $reflog = $processor->run(sprintf('git reflog show --no-abbrev %s', $currentBranch))
                                ->getStdOutAsArray();

            if (empty($reflog)) {
                $this->hashOfBranchOrigin = '0000000000000000000000000000000000000000';
            } else {
                $branchCreation = array_pop($reflog);
                [$hash] = explode(' ', trim($branchCreation));

                $this->hashOfBranchOrigin = $hash;
            }
        }

        return $this->hashOfBranchOrigin;
    }
}
