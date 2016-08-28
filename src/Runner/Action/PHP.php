<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Runner\Action;

use HookMeUp\Config;
use HookMeUp\Console\IO;
use HookMeUp\Exception\ActionExecution;
use HookMeUp\Git\Repository;
use HookMeUp\Hook\Action;

/**
 * Class PHP
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class PHP implements Action
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
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $class = $action->getAction();

        try {
            /* @var \HookMeUp\Hook\Action $exe */
            $exe = new $class();

            if (!$exe instanceof Action) {
                throw new ActionExecution('PHP class ' . $class . ' has to implement the \'Action\' interface');
            }
            $exe->execute($config, $io, $repository, $action);

        } catch (\Exception $e) {
            throw new ActionExecution('Execution failed: ' . $e->getMessage());
        } catch (\Error $e) {
            throw new ActionExecution('PHP Error: ' . $e->getMessage());
        }
    }
}
