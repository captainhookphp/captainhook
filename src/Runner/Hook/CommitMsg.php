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
use SebastianFeldmann\Git;

/**
 * CommitMsg
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 3.1.0
 */
class CommitMsg extends Hook
{
    /**
     * Hook to execute
     *
     * @var string
     */
    protected $hook = Hooks::COMMIT_MSG;

    /**
     * Read the commit message from file
     */
    public function beforeHook(): void
    {
        $commentChar = $this->repository->getConfigOperator()->getSafely('core.commentchar', '#');
        $commitMsg   = Git\CommitMessage::createFromFile($this->io->getArgument('file', ''), $commentChar);

        $this->repository->setCommitMsg($commitMsg);

        parent::beforeHook();
    }
}
