<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Action;

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
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionExecution
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);

    /**
     * Validate the message.
     *
     * @param \sebastianfeldmann\CaptainHook\Hook\Message\Validator $validator
     * @param \sebastianfeldmann\CaptainHook\Git\Repository         $repository
     */
    protected function executeValidator(Validator $validator, Repository $repository)
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $validator->validate($repository->getCommitMsg());
        }
    }
}
