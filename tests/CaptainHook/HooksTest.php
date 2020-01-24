<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App;

use PHPUnit\Framework\TestCase;

class HooksTest extends TestCase
{
    /**
     * Tests Hooks::getOriginalHookArguments
     */
    public function testHookArguments(): void
    {
        $this->assertEquals('', Hooks::getOriginalHookArguments('pre-commit'));
        $this->assertEquals(' {$PREVIOUSHEAD} {$NEWHEAD} {$MODE}', Hooks::getOriginalHookArguments('post-checkout'));
    }
}
