<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionExecution;
use CaptainHook\App\Git\Repository;
use CaptainHook\App\Hook\Action;

/**
 * Class PHP
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class PHP implements Action
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
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $class = $action->getAction();

        try {
            /* @var \CaptainHook\App\Hook\Action $exe */
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
