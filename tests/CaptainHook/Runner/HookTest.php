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

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Hooks;
use CaptainHook\App\Mockery as CHMockery;
use Exception;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    use IOMockery;
    use ConfigMockery;
    use CHMockery;
    
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

    public function testGetName(): void
    {
        $io = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo = $this->createRepositoryMock();

        $runner = new class($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $this->assertSame('pre-commit', $runner->getName());
    }

    public function testShouldSkipActionsIsFalseByDefault(): void
    {
        $io = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo = $this->createRepositoryMock();

        $runner = new class($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $this->assertFalse($runner->shouldSkipActions());
    }

    public function testShouldSkipActionsCanBeSetToTrue(): void
    {
        $io = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo = $this->createRepositoryMock();

        $runner = new class($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $this->assertTrue($runner->shouldSkipActions(true));
        $this->assertTrue($runner->shouldSkipActions());
    }

    public function testShouldSkipActionsCanBeSetToFalse(): void
    {
        $io = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo = $this->createRepositoryMock();

        $runner = new class($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $runner->shouldSkipActions(true);

        $this->assertFalse($runner->shouldSkipActions(false));
        $this->assertFalse($runner->shouldSkipActions());
    }
}
