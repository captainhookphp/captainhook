<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner;

class HookTest extends BaseTestRunner
{
    /**
     * Tests Installer::setHook
     *
     * @expectedException \Exception
     */
    public function testSetHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Hook($io, $config, $repo);
        $runner->setHook('iDonExist');
    }

    /**
     * Tests Installer::run
     */
    public function testRunHookEnabled()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $repo         = $this->getRepositoryMock();
        $actionConfig->method('getType')->willReturn('cli');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(3))->method('write');

        $runner = new Hook($io, $config, $repo);
        $runner->setHook('pre-commit');
        $runner->run();
    }

    /**
     * Tests Installer::run
     */
    public function testRunHookEnabledDisabled()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $repo         = $this->getRepositoryMock();
        $actionConfig->method('getType')->willReturn('cli');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(false);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->once())->method('write');

        $runner = new Hook($io, $config, $repo);
        $runner->setHook('pre-commit');
        $runner->run();
    }

    /**
     * Tests Hook::getActionRunner
     */
    public function testGetRunner()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $repo         = $this->getRepositoryMock();

        $hook   = new Hook($io, $config, $repo);
        $php    = $hook->getActionRunner('php');
        $cli    = $hook->getActionRunner('cli');

        $this->assertTrue(is_a($php, '\\SebastianFeldmann\\CaptainHook\\Runner\\Action\\PHP'));
        $this->assertTrue(is_a($cli, '\\SebastianFeldmann\\CaptainHook\\Runner\\Action\\Cli'));
    }

    /**
     * Tests Hook::getActionRunner
     *
     * @expectedException \Exception
     */
    public function testGetRunnerFailure()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $repo         = $this->getRepositoryMock();

        $hook   = new Hook($io, $config, $repo);
        $hook->getActionRunner('foo');
    }
}
