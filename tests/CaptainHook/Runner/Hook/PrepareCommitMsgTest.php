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
use CaptainHook\App\Git\DummyRepo;
use CaptainHook\App\Hook\Message\Action\Prepare;
use CaptainHook\App\Runner\BaseTestRunner;
use SebastianFeldmann\Git\Repository;

class PrepareCommitMsgTest extends BaseTestRunner
{
    /**
     * Tests PrepareCommitMsg::run
     */
    public function testRunHookEnabled()
    {
        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $actionConfig->method('getType')->willReturn('php');
        $actionConfig->method('getAction')->willReturn(Prepare::class);
        $actionConfig->method('getOptions')->willReturn(new Config\Options(['message' => 'Prepared commit msg']));
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(3))->method('write');

        $dummy = new DummyRepo();
        $dummy->setup();
        $repo = new Repository($dummy->getPath());

        $tmpDir = sys_get_temp_dir();
        $path   = tempnam($tmpDir, 'prepare-commit-msg');
        file_put_contents($path, 'Commit Message');

        try {
            $args   = new Config\Options(['file' => $path]);
            $runner = new PrepareCommitMsg($io, $config, $repo, $args);
            $runner->run();

            $this->assertEquals('Prepared commit msg', file_get_contents($path));

        } finally {
            unlink($path);
            $dummy->cleanup();
        }
    }

    /**
     * Tests PrepareCommitMsg::run
     */
    public function testRunHookNoMessageException()
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $args   = new Config\Options([]);
        $runner = new PrepareCommitMsg($io, $config, $repo, $args);
        $runner->run();
    }
}
