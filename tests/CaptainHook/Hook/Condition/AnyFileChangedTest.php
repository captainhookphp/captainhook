<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Condition;

use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;

class AnyFileChangedTest extends TestCase
{
    use IOMockery;
    use CHMockery;

    /**
     * Tests AnyFileChanged::isTrue
     */
    public function testIsTrue()
    {
        $io = $this->createIOMock();
        $io->expects($this->exactly(2))->method('getArgument')->willReturn('');
        $operator   = $this->createGitDiffOperator(['fiz.php', 'baz.php', 'foo.php']);
        $repository = $this->createRepositoryMock('');
        $repository->expects($this->once())->method('getDiffOperator')->willReturn($operator);

        $fileChange = new AnyFileChanged(['foo.php', 'bar.php']);

        $this->assertTrue($fileChange->isTrue($io, $repository));
    }

    /**
     * Tests AnyFileChanged::isTrue
     */
    public function testIsFalse()
    {
        $io = $this->createIOMock();
        $io->expects($this->exactly(2))->method('getArgument')->willReturn('');
        $operator   = $this->createGitDiffOperator(['fiz.php', 'baz.php']);
        $repository = $this->createRepositoryMock('');
        $repository->expects($this->once())->method('getDiffOperator')->willReturn($operator);

        $fileChange = new AnyFileChanged(['foo.php', 'bar.php']);

        $this->assertFalse($fileChange->isTrue($io, $repository));
    }
}
