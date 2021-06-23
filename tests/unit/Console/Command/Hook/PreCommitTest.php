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

class PreCommitTest extends TestCase
{
    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
    public function testExecuteLib(): void
    {
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                '--configuration' => CH_PATH_FILES . '/config/valid.json',
                '--git-directory' => $repo->getGitDir()
            ]
        );

        $cmd = new PreCommit(new Resolver());
        $cmd->run($input, $output);

        $this->assertTrue(true);
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
    public function testExecutePhar(): void
    {
        $resolver = $this->createMock(Resolver::class);
        $resolver->expects($this->once())->method('isPharRelease')->willReturn(true);

        $repo     = new DummyRepo();
        $output   = new NullOutput();
        $input    = new ArrayInput(
            [
                '--configuration' => CH_PATH_FILES . '/config/empty.json',
                '--git-directory' => $repo->getGitDir(),
                '--bootstrap'     => '../bootstrap/constant.php'
            ]
        );

        $cmd = new PreCommit($resolver);
        $cmd->run($input, $output);

        $this->assertTrue(true);
        $this->assertTrue(defined('CH_BOOTSTRAP_WORKED'));
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
    public function testExecutePharBootstrapNotFound(): void
    {
        $resolver = $this->createMock(Resolver::class);
        $resolver->expects($this->once())->method('isPharRelease')->willReturn(true);

        $repo     = new DummyRepo();
        $output   = new NullOutput();
        $input    = new ArrayInput(
            [
                '--configuration' => CH_PATH_FILES . '/config/empty.json',
                '--git-directory' => $repo->getGitDir(),
                '--bootstrap'     => '../bootstrap/fail.php'
            ]
        );

        $cmd = new PreCommit($resolver);
        $this->assertEquals(1, $cmd->run($input, $output));
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
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

        $cmd = new PreCommit($resolver);
        $cmd->run($input, $output);

        $this->assertTrue(true);
    }

    /**
     * Tests PreCommit::run
     *
     * @throws \Exception
     */
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

        $cmd = new PreCommit($resolver);
        $this->assertEquals(1, $cmd->run($input, $output));
    }
}
