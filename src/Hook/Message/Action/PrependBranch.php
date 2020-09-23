<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

/**
 * Class PrependBranch
 *
 * @package CaptainHook
 * @author  Felix Edelmann <fxedel@gmail.com>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   TODO
 */
class PrependBranch implements Action
{
    /**
     * Executes the action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $oldMsg  = $repository->getCommitMsg();

        if ($repository->isMerging()) {
            return;
        }

        $branch = $repository->getInfoOperator()->getCurrentBranch();
        $enclosedBranch = '[' . $branch . ']';

        if (substr($oldMsg->getRawContent(), 0, strlen($enclosedBranch)) === $enclosedBranch) {
            // branch is already prepended
            return;
        }

        $newMsg = new CommitMessage($enclosedBranch . ' ' . $oldMsg->getRawContent(), $oldMsg->getCommentCharacter());
        $repository->setCommitMsg($newMsg);
    }
}
