<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Condition;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Condition;
use SebastianFeldmann\Git\Repository;

/**
 * Class FileChange
 *
 * The FileChange condition in applicable for `post-merge` and `post-checkout` hooks.
 * For example it can be used to trigger an automatic composer install of the composer.json
 * or composer.lock file is changed during a checkout or merge.
 *
 * Example configuration:
 *
 * "action": "composer install"
 * "conditions": [
 *   {"exec": "\\CaptainHook\\App\\Hook\\Condition\\FileChange",
 *    "args": {
 *      "files": [
 *        "composer.json",
 *        "composer.lock"
 *      ]}}
 * ]
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.2.0
 */
class AnyFileChanged implements Condition
{
    /**
     * @var string[]
     */
    private $filesToWatch;

    /**
     * FileChange constructor.
     *
     * @param string[] $files
     */
    public function __construct(array $files)
    {
        $this->filesToWatch = $files;
    }

    /**
     * Evaluates a condition
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \SebastianFeldmann\Git\Repository $repository
     * @return bool
     */
    public function isTrue(IO $io, Repository $repository): bool
    {
        return $this->didFileChange($this->getChangedFiles($io, $repository));
    }

    /**
     * Use 'diff-tree' to find the changed files after this merge or checkout
     *
     * In case of a checkout it is easy because the arguments 'previousHead' and 'newHead' exist.
     * In case of a merge determining this hashes is more difficult so we are using the 'ref-log'
     * to do it and using 'HEAD@{1}' as the last position before the merge and 'HEAD' as the
     * current position after the merge.
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \SebastianFeldmann\Git\Repository $repository
     * @return array|string[]
     */
    private function getChangedFiles(IO $io, Repository $repository)
    {
        $oldHash = $io->getArgument('previousHead', 'HEAD@{1}');
        $newHash = $io->getArgument('newHead', 'HEAD');

        return $repository->getDiffOperator()->getChangedFiles($oldHash, $newHash);
    }

    /**
     * Check if the configured files where changed within the applied change set
     *
     * @param  array  $changedFiles
     * @return bool
     */
    private function didFileChange(array $changedFiles) : bool
    {
        foreach ($this->filesToWatch as $file) {
            if (in_array($file, $changedFiles)) {
                return true;
            }
        }
        return false;
    }
}
