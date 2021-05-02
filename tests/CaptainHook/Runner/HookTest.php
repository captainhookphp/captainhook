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

use CaptainHook\App\Config;
use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use CaptainHook\App\Mockery as CHMockery;
use CaptainHook\App\Plugin\DummyConstrainedPlugin;
use CaptainHook\App\Plugin\DummyConstrainedHookPlugin;
use CaptainHook\App\Plugin\DummyConstrainedHookPluginAlt;
use CaptainHook\App\Plugin\DummyPlugin;
use CaptainHook\App\Plugin\DummyHookPlugin;
use CaptainHook\App\Plugin\DummyHookPluginSkipsActions;
use Exception;
use PHPUnit\Framework\TestCase;

class HookTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
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

        $runner = new class ($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $this->assertSame('pre-commit', $runner->getName());
    }

    public function testShouldSkipActionsIsFalseByDefault(): void
    {
        $io = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo = $this->createRepositoryMock();

        $runner = new class ($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $this->assertFalse($runner->shouldSkipActions());
    }

    public function testShouldSkipActionsCanBeSetToTrue(): void
    {
        $io = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo = $this->createRepositoryMock();

        $runner = new class ($io, $config, $repo) extends Hook {
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

        $runner = new class ($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };

        $runner->shouldSkipActions(true);

        $this->assertFalse($runner->shouldSkipActions(false));
        $this->assertFalse($runner->shouldSkipActions());
    }

    public function testRunHookWithPlugins(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $restriction1 = new Restriction(Hooks::POST_CHECKOUT);
        DummyConstrainedPlugin::$restriction = $restriction1;
        DummyConstrainedHookPluginAlt::$restriction = $restriction1;

        $restriction2 = new Restriction(Hooks::PRE_COMMIT);
        DummyConstrainedHookPlugin::$restriction = $restriction2;

        $pluginConfig1 = new Config\Plugin(DummyPlugin::class);
        $pluginConfig2 = new Config\Plugin(DummyHookPlugin::class);
        $pluginConfig3 = new Config\Plugin(DummyConstrainedPlugin::class);
        $pluginConfig4 = new Config\Plugin(DummyConstrainedHookPlugin::class);
        $pluginConfig5 = new Config\Plugin(DummyConstrainedHookPluginAlt::class);

        $config = $this->createConfigMock();
        $config->method('failOnFirstError')->willReturn(true);
        $config->method('getPlugins')->willReturn([
            $pluginConfig1,
            $pluginConfig2,
            $pluginConfig3,
            $pluginConfig4,
            $pluginConfig5,
        ]);

        $io = $this->createIOMock();
        $repo = $this->createRepositoryMock();
        $hookConfig = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->atLeastOnce())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig, $actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $runner = new class ($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };
        $runner->run();

        $this->assertSame(1, $pluginConfig2->getPlugin()->beforeHookCalled);
        $this->assertSame(2, $pluginConfig2->getPlugin()->beforeActionCalled);
        $this->assertSame(2, $pluginConfig2->getPlugin()->afterActionCalled);
        $this->assertSame(1, $pluginConfig2->getPlugin()->afterHookCalled);

        $this->assertSame(1, $pluginConfig4->getPlugin()->beforeHookCalled);
        $this->assertSame(2, $pluginConfig4->getPlugin()->beforeActionCalled);
        $this->assertSame(2, $pluginConfig4->getPlugin()->afterActionCalled);
        $this->assertSame(1, $pluginConfig4->getPlugin()->afterHookCalled);

        $this->assertSame(0, $pluginConfig5->getPlugin()->beforeHookCalled);
        $this->assertSame(0, $pluginConfig5->getPlugin()->beforeActionCalled);
        $this->assertSame(0, $pluginConfig5->getPlugin()->afterActionCalled);
        $this->assertSame(0, $pluginConfig5->getPlugin()->afterHookCalled);
    }

    public function testRunHookSkipsActionsFromPluginBeforeHook(): void
    {
        $pluginConfig = new Config\Plugin(DummyHookPluginSkipsActions::class);
        $pluginConfig->getPlugin()->skipStartAt = 1;

        $config = $this->createConfigMock();
        $config->method('failOnFirstError')->willReturn(true);
        $config->method('getPlugins')->willReturn([$pluginConfig]);

        $io = $this->createIOMock();
        $repo = $this->createRepositoryMock();
        $hookConfig = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $hookConfig->expects($this->atLeastOnce())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([
            $actionConfig,
            $actionConfig,
        ]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $runner = new class ($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };
        $runner->run();

        $this->assertSame(1, $pluginConfig->getPlugin()->beforeHookCalled);
        $this->assertSame(0, $pluginConfig->getPlugin()->beforeActionCalled);
        $this->assertSame(0, $pluginConfig->getPlugin()->afterActionCalled);
        $this->assertSame(1, $pluginConfig->getPlugin()->afterHookCalled);
    }

    public function testRunHookSkipsActionsFromPluginBeforeAction(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $pluginConfig = new Config\Plugin(DummyHookPluginSkipsActions::class);
        $pluginConfig->getPlugin()->skipStartIn = 'beforeAction';
        $pluginConfig->getPlugin()->skipStartAt = 3;

        $config = $this->createConfigMock();
        $config->method('failOnFirstError')->willReturn(true);
        $config->method('getPlugins')->willReturn([$pluginConfig]);

        $io = $this->createIOMock();
        $repo = $this->createRepositoryMock();
        $hookConfig = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->atLeastOnce())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([
            $actionConfig,
            $actionConfig,
            $actionConfig,
            $actionConfig,
            $actionConfig,
        ]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $runner = new class ($io, $config, $repo) extends Hook {
            protected $hook = Hooks::PRE_COMMIT;
        };
        $runner->run();

        $this->assertSame(1, $pluginConfig->getPlugin()->beforeHookCalled);
        $this->assertSame(3, $pluginConfig->getPlugin()->beforeActionCalled);
        $this->assertSame(2, $pluginConfig->getPlugin()->afterActionCalled);
        $this->assertSame(1, $pluginConfig->getPlugin()->afterHookCalled);
    }
}
