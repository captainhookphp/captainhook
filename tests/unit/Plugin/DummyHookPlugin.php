<?php

namespace CaptainHook\App\Plugin;

use CaptainHook\App\Config;
use CaptainHook\App\Runner\Hook as RunnerHook;

class DummyHookPlugin extends Hook\Base
{
    public static $beforeHookCalled = 0;
    public static $beforeActionCalled = 0;
    public static $afterActionCalled = 0;
    public static $afterHookCalled = 0;

    public function beforeHook(RunnerHook $hook): void
    {
        self::$beforeHookCalled++;
    }

    public function beforeAction(RunnerHook $hook, Config\Action $action): void
    {
        self::$beforeActionCalled++;
    }

    public function afterAction(RunnerHook $hook, Config\Action $action): void
    {
        self::$afterActionCalled++;
    }

    public function afterHook(RunnerHook $hook): void
    {
        self::$afterHookCalled++;
    }
}
