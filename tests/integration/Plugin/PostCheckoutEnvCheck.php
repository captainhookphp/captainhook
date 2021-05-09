<?php

namespace CaptainHook\App\Integration\Plugin;

use CaptainHook\App\Config;
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use CaptainHook\App\Plugin\Hook as HookPlugin;
use CaptainHook\App\Plugin\Hook\Base;
use CaptainHook\App\Runner\Hook as RunnerHook;

class PostCheckoutEnvCheck extends Base implements HookPlugin, Constrained
{
    public function beforeHook(RunnerHook $hook): void
    {
        file_put_contents('env.txt', var_export(getenv(), true));
    }

    public function beforeAction(RunnerHook $hook, Config\Action $action): void
    {
    }

    public function afterAction(RunnerHook $hook, Config\Action $action): void
    {
    }

    public function afterHook(RunnerHook $hook): void
    {
    }

    public static function getRestriction(): Restriction
    {
        return new Restriction(Hooks::POST_CHECKOUT);
    }
}
