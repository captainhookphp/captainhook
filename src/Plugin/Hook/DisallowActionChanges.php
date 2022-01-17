<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Plugin\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Plugin;
use CaptainHook\App\Plugin\Hook\Exception\ActionChangedFiles;
use CaptainHook\App\Runner\Hook as RunnerHook;
use SebastianFeldmann\Git\Diff\File;

/**
 * DisallowActionChanges runner plugin
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.11.0.
 */
class DisallowActionChanges extends PreserveWorkingTree implements Plugin\Hook
{
    /**
     * @var iterable
     */
    private $priorDiff = [];

    /**
     * An array of actions that made changes to files. Each action name is the
     * key for an array of file changes made by that action.
     *
     * @var array<string, File[]>
     */
    private $actionChanges = [];

    public function beforeHook(RunnerHook $hook): void
    {
        parent::beforeHook($hook);

        // Get a diff of the current state of the working tree. Since we ran
        // the parent beforeHook(), which moves changes out of the working
        // tree, this should be an empty diff.
        $this->priorDiff = $this->repository->getDiffOperator()->compareTo();
    }

    public function afterAction(RunnerHook $hook, Config\Action $action): void
    {
        $afterDiff = $this->repository->getDiffOperator()->compareTo();

        // Did this action make any changes?
        if ($afterDiff != $this->priorDiff) {
            $this->actionChanges[$action->getAction()] = $afterDiff;
        }

        $this->priorDiff = $afterDiff;
    }

    public function afterHook(RunnerHook $hook): void
    {
        parent::afterHook($hook);

        if (count($this->actionChanges) > 0) {
            $message = '';
            foreach ($this->actionChanges as $action => $changes) {
                $message .= '<error>Action \'' . $action
                    . '\' on hook ' . $hook->getName()
                    . ' modified files</error>'
                    . PHP_EOL;
            }

            throw new ActionChangedFiles($message);
        }
    }
}
