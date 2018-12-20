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
    public function testRunHookEnabled()
    {
        $io       = $this->getIOMock();
        $config   = $this->getConfigMock();
        $configOp = $this->createMock(\SebastianFeldmann\Git\Operator\Config::class);
        $configOp->expects($this->once())->method('getSafely')->willReturn('#');

        $repo = $this->getRepositoryMock();
        $repo->expects($this->once())->method('getConfigOperator')->willReturn($configOp);

        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $actionConfig->method('getType')->willReturn('cli');
        $actionConfig->method('getAction')->willReturn(CH_PATH_FILES . '/bin/success');
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(4))->method('write');

        $args   = new Config\Options(['file' => CH_PATH_FILES . '/git/message/valid.txt']);
        $runner = new CommitMsg($io, $config, $repo, $args);
        $runner->run();
    }

    /**
     * Tests CommitMsg::run
     */
    public function testRunWithoutCommitMsgFile()
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $args   = new Config\Options([]);
        $runner = new CommitMsg($io, $config, $repo, $args);
        $runner->run();
    }
}
