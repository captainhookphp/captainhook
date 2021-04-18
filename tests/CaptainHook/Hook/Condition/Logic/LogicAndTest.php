<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Condition\Logic;

use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Hook\Condition;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class LogicalAndTest extends TestCase
{
    use IOMockery;
    use AppMockery;

    /**
     * @covers \CaptainHook\App\Hook\Condition\Logic\LogicAnd::isTrue
     * @covers \CaptainHook\App\Hook\Condition\Logic\LogicAnd::fromConditionsArray
     */
    public function testLogicAndReturnsFalseWithOneFailure(): void
    {
        $io         = $this->createIOMock();
        $repository = $this->createRepositoryMock();
        $true       = $this->getMockBuilder(Condition::class)->getMock();
        $false      = $this->getMockBuilder(Condition::class)->getMock();

        $true->method('isTrue')->willReturn(true);
        $false->method('isTrue')->willReturn(false);

        $condition = LogicAnd::fromConditionsArray([$true, $false]);

        $this->assertFalse($condition->isTrue($io, $repository));
    }

    /**
     * @covers \CaptainHook\App\Hook\Condition\Logic\LogicAnd::isTrue
     * @covers \CaptainHook\App\Hook\Condition\Logic\LogicAnd::fromConditionsArray
     */
    public function testLogicAndReturnsTrueWithAllSuccess(): void
    {
        $io         = $this->createIOMock();
        $repository = $this->createRepositoryMock();
        $true       = $this->getMockBuilder(Condition::class)->getMock();

        $true->method('isTrue')->willReturn(true);

        $condition = LogicAnd::fromConditionsArray([$true, $true, $true]);

        $this->assertTrue($condition->isTrue($io, $repository));
    }

    /**
     * @covers \CaptainHook\App\Hook\Condition\Logic\LogicAnd::fromConditionsArray
     */
    public function testNamedConstructorIgnoresNonCondition(): void
    {
        $io         = $this->createIOMock();
        $repository = $this->createRepositoryMock();
        $true       = $this->getMockBuilder(Condition::class)->getMock();
        $true->expects($this->exactly(2))->method('isTrue')->willReturn(true);

        $condition = LogicAnd::fromConditionsArray([$true, 'string', $true]);

        $this->assertTrue($condition->isTrue($io, $repository));
    }
}
