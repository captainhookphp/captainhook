<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Condition;

use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use SebastianFeldmann\Git\Repository;

/**
 * Class FileChange
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.2.0
 */
abstract class FileChanged extends File
{
    /**
     * List of file to watch
     *
     * @var array<string>
     */
    protected $filesToWatch;

    /**
     * FileChange constructor
     *
     * @param array<string> $files
     */
    public function __construct(array $files)
    {
        $this->filesToWatch = $files;
    }

    /**
     * Return the hook restriction information
     *
     * @return \CaptainHook\App\Hook\Restriction
     */
    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::POST_CHECKOUT, Hooks::POST_MERGE, Hooks::POST_REWRITE]);
    }

    /**
     * Evaluates a condition
     *
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @return bool
     */
    abstract public function isTrue(IO $io, Repository $repository): bool;

    /**
     * Use 'diff-tree' to find the changed files after this merge or checkout
     *
     * In case of a checkout it is easy because the arguments 'previousHead' and 'newHead' exist.
     * In case of a merge determining this hashes is more difficult so we are using the 'ref-log'
     * to do it and using 'HEAD@{1}' as the last position before the merge and 'HEAD' as the
     * current position after the merge.
     *
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @return array<string>
     */
    protected function getChangedFiles(IO $io, Repository $repository)
    {

        $oldHash = $this->findPreviousHead($io);
        $newHash = $io->getArgument('newHead', 'HEAD');

        return $repository->getDiffOperator()->getChangedFiles($oldHash, $newHash);
    }

    /**
     * Detects the previous head commit hash
     *
     * @param \CaptainHook\App\Console\IO $io
     * @return string
     */
    private function findPreviousHead(IO $io): string
    {
        // Check if a list of rewritten commits is supplied via stdIn.
        // This happens if the 'post-rewrite' hook is triggered.
        // The stdIn is formatted like this:
        //
        // old-hash new-hash extra-info
        // old-hash new-hash extra-info
        // ...
        $stdIn = $io->getStandardInput();
        if (!empty($stdIn)) {
            $info = explode(' ', $stdIn[0]);
            // If we find a rewritten commit, we return the first commit before the rewritten one.
            // If we do not find any rewritten commits (awkward) we use the last ref-log position.
            return isset($info[1]) ? $info[1] . '^' :  'HEAD@{1}';
        }
        return $io->getArgument('previousHead', 'HEAD@{1}');
    }
}
