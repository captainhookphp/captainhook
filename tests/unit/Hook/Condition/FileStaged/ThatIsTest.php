<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Condition\FileStaged;

use PHPUnit\Framework\TestCase;
use CaptainHook\App\Mockery as AppMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;

class ThatIsTest extends TestCase
{
    use AppMockery;
    use IOMockery;

    /**
     * Tests ThatIs::getRestriction
     */
    public function testPreCommitRestriction(): void
    {
        $this->assertTrue(ThatIs::getRestriction()->isApplicableFor('pre-commit'));
        $this->assertFalse(ThatIs::getRestriction()->isApplicableFor('pre-push'));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedTrueType(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo.php', 'bar.php']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['ofType' => 'php']);
        $this->assertTrue($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedTrueMultipleType(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo.php', 'bar.php']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['ofType' => ['php', 'js']]);
        $this->assertTrue($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedFalseMultipleType(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo.php', 'bar.php']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['ofType' => ['ts', 'js']]);
        $this->assertFalse($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedTrueDirectory(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo/foo.php', 'bar/bar.js', 'fiz/baz.txt']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['inDirectory' => 'bar/']);
        $this->assertTrue($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedFalsePartialDirectory(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo/foo.php', 'foo/bar/bar.js', 'fiz/baz.txt']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['inDirectory' => 'bar/']);
        $this->assertFalse($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedTrueMultipleDirectory(): void
    {
        $io = $this->createIOMock();
        $repo = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo/foo.php', 'bar/bar.js', 'fiz/baz.txt']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['inDirectory' => ['bar/', 'baz/']]);
        $this->assertTrue($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedFalseMultipleDirectory(): void
    {
        $io = $this->createIOMock();
        $repo = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo/foo.php', 'bar/bar.js', 'fiz/baz.txt']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['inDirectory' => ['foobar/', 'baz/']]);
        $this->assertFalse($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs::isTrue
     */
    public function testStagedFalseDirectoryAndType(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo/foo.php', 'bar/bar.js']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['inDirectory' => 'bar/', 'ofType' => 'php']);
        $this->assertFalse($thatIs->isTrue($io, $repo));
    }

    /**
     * Tests ThatIs:isTrue
     */
    public function testStagedFalse(): void
    {
        $io    = $this->createIOMock();
        $repo  = $this->createRepositoryMock();
        $index = $this->createGitIndexOperator(['foo.php']);
        $repo->expects($this->once())->method('getIndexOperator')->willReturn($index);

        $thatIs = new ThatIs(['ofType' => 'js']);
        $this->assertFalse($thatIs->isTrue($io, $repo));
    }
}
