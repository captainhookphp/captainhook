<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

use CaptainHook\App\Runner\Action\PHP;
use CaptainHook\App\Runner\Action\Cli;

class HookTest extends BaseTestRunner
{
    /**
     * Tests Hook::getActionRunner
     */
    public function testGetExecMethod()
    {
        $php = Hook::getExecMethod('php');
        $cli = Hook::getExecMethod('cli');

        $this->assertEquals('executePhpAction', $php);
        $this->assertEquals('executeCliAction', $cli);
    }

    /**
     * Tests Hook::getActionRunner
     *
     * @expectedException \Exception
     */
    public function testGetRunnerFailure()
    {
        Hook::getExecMethod('foo');
    }
}
