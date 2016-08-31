<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Hook\Message;

use HookMeUp\App\Config;
use HookMeUp\App\Console\IO;
use HookMeUp\App\Git\Repository;
use HookMeUp\App\Hook\Action;

/**
 * Class Base
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class Base implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \HookMeUp\App\Config         $config
     * @param  \HookMeUp\App\Console\IO     $io
     * @param  \HookMeUp\App\Git\Repository $repository
     * @param  \HookMeUp\App\Config\Action  $action
     * @throws \HookMeUp\App\Exception\ActionExecution
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);

    /**
     * Validate the message.
     *
     * @param \HookMeUp\App\Hook\Message\Validator $validator
     * @param \HookMeUp\App\Git\Repository         $repository
     */
    protected function executeValidator(Validator $validator, Repository $repository)
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $validator->validate($repository->getCommitMsg());
        }
    }
}
