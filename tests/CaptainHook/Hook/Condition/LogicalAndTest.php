<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\App\Hook\Condition;

use CaptainHook\App\Console\IO\DefaultIO;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Hook\Condition;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class LogicalAndTest extends TestCase
{
    use IOMockery;
    use AppMockery;

    /**
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::isTrue
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::fromConditionsArray
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::__construct
     */
    public function testBooleanAndReturnsFalseWithOneSuccess(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $true         = $this->getMockBuilder(Condition::class)->getMock();
        $false        = $this->getMockBuilder(Condition::class)->getMock();

        $true->method('isTrue')->willReturn(true);
        $false->method('isTrue')->willReturn(false);

        $condition = LogicalAnd::fromConditionsArray([$true, $false]);

        $this->assertFalse($condition->isTrue($io, $repository));
    }

    /**
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::isTrue
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::fromConditionsArray
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::__construct
     */
    public function testLogicalAndReturnsTrueWithAllSuccess(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $true         = $this->getMockBuilder(Condition::class)->getMock();

        $true->method('isTrue')->willReturn(true);

        $condition = LogicalAnd::fromConditionsArray([$true, $true, $true]);

        $this->assertTrue($condition->isTrue($io, $repository));
    }

    /**
     * @covers \CaptainHook\App\Hook\Condition\LogicalAnd::fromConditionsArray
     */
    public function testNamedConstructorIgnoresNonCondition(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $true         = $this->getMockBuilder(Condition::class)->getMock();
        $true->expects($this->exactly(2))->method('isTrue')->willReturn(true);

        $condition = LogicalAnd::fromConditionsArray([$true, 'string', $true]);

        $this->assertTrue($condition->isTrue($io, $repository));
    }
}
