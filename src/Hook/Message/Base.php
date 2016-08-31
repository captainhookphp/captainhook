<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Hook\Message;

use CaptainHook\Config;
use CaptainHook\Console\IO;
use CaptainHook\Git\Repository;
use CaptainHook\Hook\Action;

/**
 * Class Base
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Base implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);

    /**
     * Validate the message.
     *
     * @param \CaptainHook\Hook\Message\Validator $validator
     * @param \CaptainHook\Git\Repository         $repository
     */
    protected function executeValidator(Validator $validator, Repository $repository)
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $validator->validate($repository->getCommitMsg());
        }
    }
}
