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
    public function testRunHookEnabled(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $actionConfig->method('getAction')->willReturn('\\' . Prepare::class);
        $actionConfig->method('getOptions')->willReturn(new Config\Options(['message' => 'Prepared commit msg']));
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);

        $dummy = new DummyRepo();
        $dummy->setup();
        $repo = new Repository($dummy->getPath());

        $tmpDir = sys_get_temp_dir();
        $path   = tempnam($tmpDir, 'prepare-commit-msg');
        file_put_contents($path, 'Commit Message');

        $io->expects($this->exactly(2))->method('write');
        $io->expects($this->exactly(3))->method('getArgument')->willReturnOnConsecutiveCalls(
            $path,
            '',
            ''
        );

        try {
            $runner = new PrepareCommitMsg($io, $config, $repo);
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
    public function testRunHookNoMessageException(): void
    {
        $this->expectException(\Exception::class);

        $io           = $this->getIOMock();
        $config       = $this->getConfigMock();
        $repo         = $this->getRepositoryMock();
        $hookConfig   = $this->getHookConfigMock();
        $actionConfig = $this->getActionConfigMock();
        $actionConfig->method('getAction')->willReturn(Prepare::class);
        $actionConfig->method('getOptions')->willReturn(new Config\Options(['message' => 'Prepared commit msg']));
        $hookConfig->expects($this->once())->method('isEnabled')->willReturn(true);
        $hookConfig->expects($this->once())->method('getActions')->willReturn([$actionConfig]);
        $config->expects($this->once())->method('getHookConfig')->willReturn($hookConfig);
        $io->expects($this->exactly(3))->method('getArgument')->willReturn('');

        $runner = new PrepareCommitMsg($io, $config, $repo);
        $runner->run();
    }
}
