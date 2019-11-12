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

class CommitMsgTest extends BaseTestRunner
{
    /**
     * Tests CommitMsg::run
     */
    public function testRunHookEnabled(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $io       = $this->getIOMock();
        $config   = $this->getConfigMock();
        $configOp = $this->createMock(\SebastianFeldmann\Git\Operator\Config::class);
        $configOp->expects($this->once())->method('getSafely')->willReturn('#');

        $repo = $this->getRepositoryMock();
        $repo->expects($this->once())->method('getConfigOperator')->willReturn($configOp);

        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(3))->method('write');
        $io->expects($this->once())->method('getArgument')->willReturn(CH_PATH_FILES . '/git/message/valid.txt');

        $runner = new CommitMsg($io, $config, $repo);
        $runner->run();
    }

    /**
     * Tests CommitMsg::run
     */
    public function testRunWithoutCommitMsgFile(): void
    {
        $this->expectException(\Exception::class);

        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $hookConfig   = $this->getHookConfigMock();
        $repo         = $this->getRepositoryMock();
        $actionConfig = $this->getActionConfigMock();
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->once())->method('getArgument')->willReturn('');

        $runner = new CommitMsg($io, $config, $repo);
        $runner->run();
    }
}
