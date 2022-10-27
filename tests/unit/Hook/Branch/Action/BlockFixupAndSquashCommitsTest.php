<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Branch\Action;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Config\Options;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Git\Log\Commit;

class BlockFixupAndSquashCommitsTest extends TestCase
{
    use AppMockery;
    use IOMockery;
    use ConfigMockery;

    /**
     * Tests BlockFixupAndSquashCommits::getRestriction
     */
    public function testConstraint(): void
    {
        $this->assertTrue(BlockFixupAndSquashCommits::getRestriction()->isApplicableFor('pre-push'));
        $this->assertFalse(BlockFixupAndSquashCommits::getRestriction()->isApplicableFor('pre-commit'));
    }

    /**
     * Tests BlockFixupAndSquashCommits::execute
     */
    public function testExecuteSuccess(): void
    {
        $input    = ['refs/heads/main 12345 refs/heads/main 98765'];
        $io       = $this->createIOMock();
        $repo     = $this->createRepositoryMock();
        $config   = $this->createConfigMock();
        $action   = $this->createActionConfigMock();
        $operator = $this->createGitLogOperator();
        $action->expects($this->once())->method('getOptions')->willReturn(new Options([]));
        $io->expects($this->once())->method('getStandardInput')->willReturn($input);
        $operator->method('getCommitsBetween')->willReturn($this->getFakeCommits());
        $repo->method('getLogOperator')->willReturn($operator);

        $blocker = new BlockFixupAndSquashCommits();
        $blocker->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests BlockFixupAndSquashCommits::execute
     */
    public function testExecuteBlockFixup(): void
    {
        $this->expectException(\Exception::class);

        $input    = ['refs/heads/main 12345 refs/heads/main 98765'];
        $io       = $this->createIOMock();
        $repo     = $this->createRepositoryMock();
        $config   = $this->createConfigMock();
        $action   = $this->createActionConfigMock();
        $operator = $this->createGitLogOperator();
        $action->expects($this->once())->method('getOptions')->willReturn(new Options([]));
        $io->expects($this->once())->method('getStandardInput')->willReturn($input);
        $operator->method('getCommitsBetween')->willReturn($this->getFakeCommits('fixup! Foo'));
        $repo->method('getLogOperator')->willReturn($operator);

        $blocker = new BlockFixupAndSquashCommits();
        $blocker->execute($config, $io, $repo, $action);
    }

    /**
     * Tests BlockFixupAndSquashCommits::execute
     */
    public function testExecuteBlockSquash(): void
    {
        $this->expectException(\Exception::class);

        $input    = ['refs/heads/main 12345 refs/heads/main 98765'];
        $io       = $this->createIOMock();
        $repo     = $this->createRepositoryMock();
        $config   = $this->createConfigMock();
        $action   = $this->createActionConfigMock();
        $operator = $this->createGitLogOperator();
        $action->expects($this->once())->method('getOptions')->willReturn(new Options([]));
        $io->expects($this->once())->method('getStandardInput')->willReturn($input);
        $operator->method('getCommitsBetween')->willReturn($this->getFakeCommits('squash! Foo'));
        $repo->method('getLogOperator')->willReturn($operator);

        $blocker = new BlockFixupAndSquashCommits();
        $blocker->execute($config, $io, $repo, $action);
    }

    /**
     * Tests BlockFixupAndSquashCommits::execute
     */
    public function testExecuteNoPushInfo(): void
    {
        $input    = [''];
        $io       = $this->createIOMock();
        $repo     = $this->createRepositoryMock();
        $config   = $this->createConfigMock();
        $action   = $this->createActionConfigMock();
        $io->expects($this->once())->method('getStandardInput')->willReturn($input);

        $blocker = new BlockFixupAndSquashCommits();
        $blocker->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }


    /**
     * @return array<\SebastianFeldmann\Git\Log\Commit>
     */
    private function getFakeCommits(string $subject = ''): array
    {
        return [
            new Commit('12345', [], 'Test commit #1', '', new \DateTimeImmutable(), ''),
            new Commit('98765', [], $subject, '', new \DateTimeImmutable(), ''),
        ];
    }
}
