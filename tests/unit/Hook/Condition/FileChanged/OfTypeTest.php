<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Condition\FileChanged;

use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;

class OfTypeTest extends TestCase
{
    use IOMockery;
    use CHMockery;

    /**
     * Tests OfType::getRestriction
     */
    public function testRestriction(): void
    {
        $this->assertTrue(OfType::getRestriction()->isApplicableFor('pre-push'));
        $this->assertFalse(OfType::getRestriction()->isApplicableFor('pre-commit'));
    }

    /**
     * Tests OfType::isTrue
     */
    public function testIsTrue(): void
    {
        $io = $this->createIOMock();
        $io->method('getArgument')->willReturn('');
        $io->expects($this->atLeastOnce())
           ->method('getStandardInput')
           ->willReturn(
               [
                   'refs/heads/main 9dfa0fa6221d75f48b2dfac359127324bedf8409' .
                   ' refs/heads/main 8309f6e16097754469c485e604900c573bf2c5d8'
               ]
           );
        $operator   = $this->createGitDiffOperator();
        $repository = $this->createRepositoryMock('');
        $operator->method('getChangedFilesOfType')->willReturn(['fiz.php', 'foo.txt']);
        $repository->expects($this->once())->method('getDiffOperator')->willReturn($operator);

        $fileChange = new OfType('php');
        $this->assertTrue($fileChange->isTrue($io, $repository));
    }


    /**
     * Tests OfType::isTrue
     */
    public function testIsZeroHash(): void
    {
        $io = $this->createIOMock();
        $io->method('getArgument')->willReturn('');
        $io->expects($this->atLeastOnce())
            ->method('getStandardInput')
            ->willReturn(
                [
                    'refs/heads/main 9dfa0fa6221d75f48b2dfac359127324bedf8409' .
                    ' refs/heads/main 0000000000000000000000000000000000000000'
                ]
            );

        $repository = $this->createRepositoryMock('');

        $fileChange = new OfType('php');
        $this->assertFalse($fileChange->isTrue($io, $repository));
    }

    /**
     * Tests OfType::isTrue
     */
    public function testIsFalse(): void
    {
        $io = $this->createIOMock();
        $io->method('getArgument')->willReturn('');
        $io->expects($this->atLeastOnce())
            ->method('getStandardInput')
            ->willReturn(
                [
                    'refs/heads/main 9dfa0fa6221d75f48b2dfac359127324bedf8409' .
                    ' refs/heads/main 8309f6e16097754469c485e604900c573bf2c5d8'
                ]
            );
        $operator   = $this->createGitDiffOperator();
        $repository = $this->createRepositoryMock('');
        $operator->method('getChangedFilesOfType')->willReturn([]);
        $repository->expects($this->once())->method('getDiffOperator')->willReturn($operator);

        $fileChange = new OfType('php');
        $this->assertFalse($fileChange->isTrue($io, $repository));
    }
}
