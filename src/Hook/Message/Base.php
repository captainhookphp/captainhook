<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Git\Repository;
use CaptainHook\App\Hook\Action;

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
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \CaptainHook\App\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \CaptainHook\App\Exception\ActionExecution
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);

    /**
     * Validate the message.
     *
     * @param \CaptainHook\App\Hook\Message\Validator $validator
     * @param \CaptainHook\App\Git\Repository         $repository
     */
    protected function executeValidator(Validator $validator, Repository $repository)
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $validator->validate($repository->getCommitMsg());
        }
    }
}
