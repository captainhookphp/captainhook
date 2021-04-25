<?php

namespace CaptainHook\App\Plugin;

use CaptainHook\App\Config;
use CaptainHook\App\Runner\Hook as RunnerHook;

class DummyRunnerPlugin extends Runner\Base
{
    public $beforeHookCalled = 0;
    public $beforeActionCalled = 0;
    public $afterActionCalled = 0;
    public $afterHookCalled = 0;

    public function beforeHook(RunnerHook $hook): void
    {
        $this->beforeHookCalled++;
    }

    public function beforeAction(RunnerHook $hook, Config\Action $action): void
    {
        $this->beforeActionCalled++;
    }

    public function afterAction(RunnerHook $hook, Config\Action $action): void
    {
        $this->afterActionCalled++;
    }

    public function afterHook(RunnerHook $hook): void
    {
        $this->afterHookCalled++;
    }
}
