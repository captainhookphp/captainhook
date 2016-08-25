<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook;

use HookMeUp\Config;
use HookMeUp\Console\IO;
use HookMeUp\Git\Repository;

/**
 * Class Action
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
interface Action
{
    /**
     * Execute the configured action.
     *
     * @param  \HookMeUp\Config         $config
     * @param  \HookMeUp\Console\IO     $io
     * @param  \HookMeUp\Git\Repository $repository
     * @param  \HookMeUp\Config\Action  $action
     * @throws \HookMeUp\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);
}
