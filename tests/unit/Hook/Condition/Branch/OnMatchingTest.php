<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Condition\Branch;

use PHPUnit\Framework\TestCase;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as AppMockery;

class OnMatchingTest extends TestCase
{
    use IOMockery;
    use AppMockery;

    /**
     * Tests OnByRegex::isTrue
     */
    public function testConditionTrue(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $infoOperator = $this->createGitInfoOperator('', 'feature/ABC-1234');
        $infoOperator->expects($this->once())->method('getCurrentBranch');
        $repository->expects($this->once())->method('getInfoOperator')->willReturn($infoOperator);

        $condition = new OnMatching('#feature/[a-z0-9\-_]+#i');

        $this->assertTrue($condition->isTrue($io, $repository));
    }

    /**
     * Tests OnByRegex::isTrue
     */
    public function testConditionFalse(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $infoOperator = $this->createGitInfoOperator();
        $infoOperator->expects($this->once())->method('getCurrentBranch')->willReturn('main');
        $repository->expects($this->once())->method('getInfoOperator')->willReturn($infoOperator);

        $condition = new OnMatching('#feature/[a-z0-9\-_]+#i');

        $this->assertFalse($condition->isTrue($io, $repository));
    }
}
