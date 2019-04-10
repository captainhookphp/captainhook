<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Runner\BaseTestRunner;

class PostCheckoutTest extends BaseTestRunner
{
    /**
     * Tests PostCheckout::run
     */
    public function testRunHookEnabled()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $repo         = $this->getRepositoryMock();
        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(3))->method('write');

        $args   = new Config\Options([]);
        $runner = new PostCheckout($io, $config, $repo, $args);
        $runner->run();
    }

    /**
     * Tests PostCheckout::run
     */
    public function testRunHookDisabled()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $hookConfig   = $this->getHookConfigMock();
        $repo         = $this->getRepositoryMock();
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(false);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->once())->method('write');

        $args   = new Config\Options([]);
        $runner = new PostCheckout($io, $config, $repo, $args);
        $runner->run();
    }
}
