<?php

namespace CaptainHook\App\Integration\Plugin;

use CaptainHook\App\Config;
use CaptainHook\App\Plugin\Hook as HookPlugin;
use CaptainHook\App\Plugin\Hook\Base;
use CaptainHook\App\Runner\Hook as RunnerHook;

class SimplePlugin extends Base implements HookPlugin
{
    public function beforeHook(RunnerHook $hook): void
    {
        $stuff = $this->plugin->getOptions()->get('stuff');
        $this->io->write("Do {$stuff} before {$hook->getName()} runs");
    }

    public function beforeAction(RunnerHook $hook, Config\Action $action): void
    {
        $stuff = $this->plugin->getOptions()->get('stuff');
        $this->io->write("Do {$stuff} before action {$action->getAction()} runs");
    }

    public function afterAction(RunnerHook $hook, Config\Action $action): void
    {
        $stuff = $this->plugin->getOptions()->get('stuff');
        $this->io->write("Do {$stuff} after action {$action->getAction()} runs");
    }

    public function afterHook(RunnerHook $hook): void
    {
        $stuff = $this->plugin->getOptions()->get('stuff');
        $this->io->write("Do {$stuff} after {$hook->getName()} runs");
    }
}
