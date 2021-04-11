<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Hook;

use CaptainHook\App\Hooks;
use CaptainHook\App\Runner\Hook;
use SebastianFeldmann\Git\Status\Path;

/**
 *  Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 3.1.0
 */
class PreCommit extends Hook
{
    /**
     * Hook to execute
     *
     * @var string
     */
    protected $hook = Hooks::PRE_COMMIT;

    /**
     * Files marked in the index as "intent to add."
     *
     * @var Path[]
     */
    private $intentToAddFiles = [];

    /**
     * A file where unstaged changes are stored as a patch.
     *
     * @var string|null
     */
    private $unstagedPatchFile = null;

    public function beforeHook(): void
    {
        $this->clearIntentToAddFiles();
        $this->clearUnstagedChanges();

        parent::beforeHook();
    }

    public function afterHook(): void
    {
        $this->restoreUnstagedChanges();
        $this->restoreIntentToAddFiles();

        parent::afterHook();
    }

    /**
     * Find whether there are any files marked as intent-to-add, cache them, and
     * remove them from the index.
     *
     * @return void
     */
    private function clearIntentToAddFiles(): void
    {
        $status = $this->repository->getStatusOperator()->getWorkingTreeStatus();

        // Make sure we don't have something already set.
        $this->intentToAddFiles = [];

        foreach ($status as $path) {
            if ($path->isAddedInWorkingTree() === true) {
                $this->intentToAddFiles[] = $path;
            }
        }

        if (count($this->intentToAddFiles) === 0) {
            return;
        }

        $this->io->write('Unstaged intent-to-add files detected.');

        $this->repository->getIndexOperator()->removeFiles(
            array_map(function (Path $path): string {
                return $path->getPath();
            }, $this->intentToAddFiles),
            false,
            true
        );
    }

    /**
     * If we cached and removed from the index any files that were marked as
     * intent-to-add, restore them to the index.
     *
     * @return void
     */
    private function restoreIntentToAddFiles(): void
    {
        if (count($this->intentToAddFiles) === 0) {
            return;
        }

        $this->repository->getIndexOperator()->recordIntentToAddFiles(
            array_map(function (Path $path): string {
                return $path->getPath();
            }, $this->intentToAddFiles)
        );

        $this->io->write('Restored intent-to-add files.');

        $this->intentToAddFiles = [];
    }
}
