<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Hook;

use CaptainHook\Config;
use CaptainHook\Console\IO;
use CaptainHook\Git\Repository;

/**
 * Class Action
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
interface Action
{
    /**
     * Executes the action.
     *
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action);
}
