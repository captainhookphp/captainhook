<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO;
use SebastianFeldmann\CaptainHook\Hook\Action;
use SebastianFeldmann\CaptainHook\Hook\Message\RuleBook;
use SebastianFeldmann\Git\Repository;

/**
 * Class Book
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Book implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \SebastianFeldmann\CaptainHook\Config         $config
     * @param  \SebastianFeldmann\CaptainHook\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \SebastianFeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);

    /**
     * Validate the message.
     *
     * @param  \SebastianFeldmann\CaptainHook\Hook\Message\RuleBook $ruleBook
     * @param  \SebastianFeldmann\Git\Repository                    $repository
     * @throws \SebastianFeldmann\CaptainHook\Exception\ActionFailed
     */
    protected function validate(RuleBook $ruleBook, Repository $repository)
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $ruleBook->validate($repository->getCommitMsg());
        }
    }
}
