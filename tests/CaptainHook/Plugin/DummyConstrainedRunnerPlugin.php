<?php

namespace CaptainHook\App\Plugin;

use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Restriction;

class DummyConstrainedRunnerPlugin extends DummyRunnerPlugin implements Constrained
{
    /**
     * @var Restriction
     */
    public static $restriction;

    public static function getRestriction(): Restriction
    {
        return self::$restriction;
    }
}
