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

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;

class PostRewriteTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests PrePush::run
     *
     * @throws \Exception
     */
    public function testRunHookEnabled(): void
    {
        $io           = $this->createIOMock();
        $config       = $this->createConfigMock();
        $repo         = $this->createRepositoryMock();
        $hookConfig   = $this->createHookConfigMock();

        // configure the actually called hook
        $actionConfig = $this->createActionConfigMock();
        $hookConfig->expects($this->atLeast(1))->method('getName')->willReturn('post-rewrite');
        $hookConfig->expects($this->atLeast(1))->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);

        // configure the virtual hook
        $vHookConfig   = $this->createHookConfigMock();
        $vActionConfig = $this->createActionConfigMock();
        $vHookConfig->expects($this->atLeast(1))->method('isEnabled')->willReturn(true);
        $vHookConfig->expects($this->once())->method('getActions')->willReturn([$vActionConfig]);

        // the config wll return the actually called config and then the virtual hook config
        $config->expects($this->exactly(2))
               ->method('getHookConfig')
               ->willReturnOnConsecutiveCalls($hookConfig, $vHookConfig);

        $config->expects($this->atLeastOnce())->method('isHookEnabled')->willReturn(true);

        $config->expects($this->atLeastOnce())->method('isHookEnabled')->willReturn(true);

        $io->expects($this->atLeast(1))->method('write');

        $runner = new PostRewrite($io, $config, $repo);
        $runner->run();
    }

    /**
     * Tests PrePush::run
     *
     * @throws \Exception
     */
    public function testRunVirtualHookDisabled(): void
    {
        $io           = $this->createIOMock();
        $config       = $this->createConfigMock();
        $repo         = $this->createRepositoryMock();
        $hookConfig   = $this->createHookConfigMock();

        // configure the actually called hook
        $actionConfig = $this->createActionConfigMock();
        $hookConfig->expects($this->atLeast(1))->method('getName')->willReturn('post-rewrite');
        $hookConfig->expects($this->atLeast(1))->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);

        // configure the virtual hook
        $vHookConfig = $this->createHookConfigMock();
        $vHookConfig->expects($this->atLeast(1))->method('isEnabled')->willReturn(false);

        // the config wll return the actually called config and then the virtual hook config
        $config->expects($this->exactly(2))
               ->method('getHookConfig')
               ->willReturnOnConsecutiveCalls($hookConfig, $vHookConfig);

        $config->expects($this->atLeastOnce())->method('isHookEnabled')->willReturnCallback(function($hook){
            return $hook === 'post-rewrite';
        });

        $io->expects($this->atLeast(1))->method('write');

        $runner = new PostRewrite($io, $config, $repo);
        $runner->run();
    }
}
