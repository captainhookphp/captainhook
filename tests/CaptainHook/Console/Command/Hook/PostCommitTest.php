<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Command\Hook;

use CaptainHook\App\Console\Runtime\Resolver;
use CaptainHook\App\Git\DummyRepo;
use Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;

class PostCommitTest extends TestCase
{
    /**
     * Tests PostCommit::run
     *
     * @throws \Exception
     */
    public function testExecuteWithRelativeConfigPath(): void
    {
        $cwd = getcwd();
        chdir(CH_PATH_FILES);

        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                '--configuration' => './config/valid.json',
                '--git-directory' => $repo->getGitDir()
            ]
        );

        $cmd = new PostCommit(new Resolver());
        $cmd->run($input, $output);

        chdir($cwd);

        $this->assertTrue(true);
    }

    public function testExecuteFailingActionInDebugMode(): void
    {
        $this->expectException(Exception::class);

        $output = $this->createMock(NullOutput::class);
        $output->expects($this->once())->method('isDebug')->willReturn(true);

        $resolver = new Resolver();
        $repo     = new DummyRepo();
        $input    = new ArrayInput(
            [
                '--configuration' => CH_PATH_FILES . '/config/valid-but-failing.json',
                '--git-directory' => $repo->getGitDir(),
                '--bootstrap'     => '../bootstrap/empty.php'
            ]
        );

        $cmd = new PostCommit($resolver);
        $cmd->run($input, $output);

        $this->assertTrue(true);
    }

    public function testExecuteFailingActionInVerboseMode(): void
    {
        $output = $this->createMock(NullOutput::class);
        $output->expects($this->once())->method('isDebug')->willReturn(false);
        $output->expects($this->atLeast(1))->method('writeLn');

        $resolver = new Resolver();
        $repo     = new DummyRepo();
        $input    = new ArrayInput(
            [
                '--configuration' => CH_PATH_FILES . '/config/valid-but-failing.json',
                '--git-directory' => $repo->getGitDir(),
                '--bootstrap'     => '../bootstrap/empty.php'
            ]
        );

        $cmd = new PostCommit($resolver);
        $this->assertEquals(1, $cmd->run($input, $output));
    }
}
