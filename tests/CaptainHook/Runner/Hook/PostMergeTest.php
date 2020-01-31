<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;

class PostMergeTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests PostMerge::run
     *
     * @throws \Exception
     */
    public function testRunHookEnabled(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $io           = $this->createIOMock();
        $config       = $this->createConfigMock();
        $repo         = $this->createRepositoryMock();
        $hookConfig   = $this->createHookConfigMock();
        $actionConfig = $this->createActionConfigMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $runner = new PostMerge($io, $config, $repo);
        $runner->run();
    }

    /**
     * Tests PostMerge::run
     *
     * @throws \Exception
     */
    public function testRunHookWithConditionsApply(): void
    {
        $io              = $this->createIOMock();
        $config          = $this->createConfigMock();
        $repo            = $this->createRepositoryMock();
        $hookConfig      = $this->createHookConfigMock();
        $actionConfig    = $this->createActionConfigMock();
        $conditionConfig = new Config\Condition(CH_PATH_FILES . '/bin/success');
        $actionConfig->expects($this->once())->method('getConditions')->willReturn([$conditionConfig]);
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->atLeast(1))->method('write');

        $runner = new PostMerge($io, $config, $repo);
        $runner->run();
    }


    /**
     * Tests PostMerge::run
     *
     * @throws \Exception
     */
    public function testRunHookWithConditionsFail()
    {
        $io              = $this->createIOMock();
        $config          = $this->createConfigMock();
        $repo            = $this->createRepositoryMock();
        $hookConfig      = $this->createHookConfigMock();
        $actionConfig    = $this->createActionConfigMock();
        $conditionConfig = new Config\Condition(CH_PATH_FILES . '/bin/failure');
        $actionConfig->expects($this->once())->method('getConditions')->willReturn([$conditionConfig]);
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(3))->method('write');

        $runner = new PostMerge($io, $config, $repo);
        $runner->run();
    }

    /**
     * Tests PostMerge::run
     *
     * @throws \Exception
     */
    public function testRunHookDisabled()
    {
        $io           = $this->createIOMock();
        $config       = $this->createConfigMock();
        $hookConfig   = $this->createHookConfigMock();
        $repo         = $this->createRepositoryMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(false);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->once())->method('write');

        $runner = new PostMerge($io, $config, $repo);
        $runner->run();
    }
}
