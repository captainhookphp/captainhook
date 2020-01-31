<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner;

use Exception;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    /**
     * Tests Hook::getActionRunner
     */
    public function testGetExecMethod(): void
    {
        $php = Hook::getExecMethod('php');
        $cli = Hook::getExecMethod('cli');

        $this->assertEquals('executePhpAction', $php);
        $this->assertEquals('executeCliAction', $cli);
    }

    /**
     * Tests Hook::getActionRunner
     */
    public function testGetRunnerFailure(): void
    {
        $this->expectException(Exception::class);

        Hook::getExecMethod('foo');
    }
}
