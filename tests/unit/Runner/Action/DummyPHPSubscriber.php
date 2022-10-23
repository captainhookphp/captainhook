<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Action;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action as ActionInterface;
use CaptainHook\App\Hook\EventSubscriber as SubscriberInterface;
use SebastianFeldmann\Git\Repository;

class DummyPHPSubscriber implements ActionInterface, SubscriberInterface
{
    /**
     * Execute action without errors or exceptions
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        // do something fooish
    }

    /**
     * Return empty list of event handlers
     *
     * @param  \CaptainHook\App\Config\Action $action
     * @return array|\CaptainHook\App\Event\Handler[][]
     */
    public static function getEventHandlers(Action $action): array
    {
        return [];
    }
}
