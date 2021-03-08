<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace CaptainHook\App\Hook\Condition;

use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Hook\Condition;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class LogicalOrTest extends TestCase
{
    use IOMockery;
    use AppMockery;

    /**
     * @covers \CaptainHook\App\Hook\Condition\LogicalOr::isTrue
     * @covers \CaptainHook\App\Hook\Condition\LogicalOr::fromConditionsArray
     * @covers \CaptainHook\App\Hook\Condition\LogicalOr::__construct
     */
    public function testLogicalOrReturnsTrueWithOneSuccess(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $true         = $this->getMockBuilder(Condition::class)->getMock();
        $false        = $this->getMockBuilder(Condition::class)->getMock();

        $true->method('isTrue')->willReturn(true);
        $false->method('isTrue')->willReturn(false);

        $condition = LogicalOr::fromConditionsArray([$false, $true, $false]);

        $this->assertTrue($condition->isTrue($io, $repository));
    }

    /**
     * @covers \CaptainHook\App\Hook\Condition\LogicalOr::isTrue
     * @covers \CaptainHook\App\Hook\Condition\LogicalOr::fromConditionsArray
     * @covers \CaptainHook\App\Hook\Condition\LogicalOr::__construct
     */
    public function testLogicalOrReturnsFallsWithAllFailures(): void
    {
        $io           = $this->createIOMock();
        $repository   = $this->createRepositoryMock();
        $false         = $this->getMockBuilder(Condition::class)->getMock();

        $false->method('isTrue')->willReturn(false);

        $condition = LogicalOr::fromConditionsArray([$false, $false]);

        $this->assertFalse($condition->isTrue($io, $repository));
    }
}
