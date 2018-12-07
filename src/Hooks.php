<?php

declare(strict_types=1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\App;


final class Hooks
{
    const PRE_COMMIT = 'pre-commit';

    const PRE_PUSH = 'pre-push';

    const COMMIT_MSG = 'commit-msg';

    const PREPARE_COMMIT_MSG = 'prepare-commit-msg';

    public static function getValidHooks() : array
    {
        return [
            self::COMMIT_MSG => 1,
            self::PRE_PUSH   => 1,
            self::PRE_COMMIT => 1,
            self::PREPARE_COMMIT_MSG => 1,
        ];
    }
}