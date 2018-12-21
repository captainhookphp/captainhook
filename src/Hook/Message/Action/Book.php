<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Message\RuleBook;
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
     * Execute the configured action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action) : void;

    /**
     * Validate the message
     *
     * @param  \CaptainHook\App\Hook\Message\RuleBook $ruleBook
     * @param  \SebastianFeldmann\Git\Repository      $repository
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function validate(RuleBook $ruleBook, Repository $repository) : void
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $ruleBook->validate($repository->getCommitMsg());
        }
    }
}
