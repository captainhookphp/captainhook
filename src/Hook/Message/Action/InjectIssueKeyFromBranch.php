<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Options;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;

/**
 * Class PrepareFromFile
 *
 * Example configuration:
 * {
 *   "action": "\\CaptainHook\\App\\Hook\\Message\\Action\\InjectIssueKeyFromBranch",
 *   "options": {
 *     "regex": "#([A-Z]+\\-[0-9]+)#i",
 *     "into": "body",
 *     "mode": "append",
 *     "prefix": "\nissue: ",
 *     "force": true
 *   }
 * }
 *
 * The regex option needs group $1 (...) to be the issue key
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.16.0
 */
class InjectIssueKeyFromBranch implements Action, Constrained
{
    /**
     * Returns a list of applicable hooks
     *
     * @return \CaptainHook\App\Hook\Restriction
     */
    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::PREPARE_COMMIT_MSG]);
    }

    /**
     * Execute the configured action
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
        $branch  = $repository->getInfoOperator()->getCurrentBranch();
        $options = $action->getOptions();
        $match   = [];
        $pattern = $options->get('regex', '#([A-Z]+\-[0-9]+)#i');

        // can we actually find an issue id?
        if (!preg_match($pattern, $branch, $match)) {
            if ($options->get('force', false)) {
                throw new ActionFailed('No issue key found in branch name');
            }
            return;
        }

        $issueID = $match[1] ?? '';
        $msg     = $repository->getCommitMsg();

        // make sure the issue key is not already in our commit message
        if (stripos($msg->getSubject() . $msg->getContent(), $issueID) !== false) {
            return;
        }
        $repository->setCommitMsg($this->createNewCommitMessage($options, $msg, $issueID));
    }

    /**
     * Will create the new commit message with the injected issue key
     *
     * @param  \CaptainHook\App\Config\Options      $options
     * @param  \SebastianFeldmann\Git\CommitMessage $msg
     * @param  string                               $issueID
     * @return \SebastianFeldmann\Git\CommitMessage
     */
    private function createNewCommitMessage(Options $options, CommitMessage $msg, string $issueID): CommitMessage
    {
        // let's figure out where to put the issueID
        $target = $options->get('into', 'body');
        $mode   = $options->get('mode', 'append');
        $prefix = $options->get('prefix', ' ');

        // overwrite either subject or body
        $newMsgData          = ['subject' => $msg->getSubject(), 'body' => $msg->getBody()];
        $newMsgData[$target] = $this->injectIssueId($issueID, $newMsgData[$target], $mode, $prefix);

        $comments = '';
        foreach ($msg->getLines() as $line) {
            if (strpos(trim($line), $msg->getCommentCharacter()) === 0) {
                $comments .= $line . PHP_EOL;
            }
        }

        return new CommitMessage(
            $newMsgData['subject'] . PHP_EOL . PHP_EOL . $newMsgData['body'] . PHP_EOL . $comments,
            $msg->getCommentCharacter()
        );
    }

    /**
     * Appends or prepends the issue id to the given message part
     *
     * @param  string $issueID
     * @param  string $msg
     * @param  string $mode
     * @param  string $prefix
     * @return string
     */
    private function injectIssueId(string $issueID, string $msg, string $mode, string $prefix): string
    {
        return ltrim($mode === 'prepend' ? $prefix . $issueID . ' ' . $msg : $msg . $prefix . $issueID);
    }
}
