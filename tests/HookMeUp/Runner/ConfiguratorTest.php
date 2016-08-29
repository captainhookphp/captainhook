<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Runner;

class ConfiguratorTest extends BaseTestRunner
{
    /**
     * Tests Installer::installHook
     */
    public function testConfigureCliHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Configurator($io, $config, $repo);
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', 'echo \'foo\''. 'n'));
        $io->expects($this->once())->method('askAndValidate')->willReturn('cli');
        $runner->configureHook($config, 'pre-push', true);
    }

    /**
     * Tests Installer::installHook
     */
    public function testConfigurePHPHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Configurator($io, $config, $repo);
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', 'echo \'foo\''. 'n'));
        $io->expects($this->once())->method('askAndValidate')->willReturn('php');
        $runner->configureHook($config, 'pre-push', true);
    }
}
