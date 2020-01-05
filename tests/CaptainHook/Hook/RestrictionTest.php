<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook;

use PHPUnit\Framework\TestCase;

class RestrictionTest extends TestCase
{
    /**
     * Tests Restriction::isApplicable
     */
    public function testIsApplicableFromArray(): void
    {
        $restriction = Restriction::fromArray(['pre-commit', 'pre-push']);

        $this->assertTrue($restriction->isApplicableFor('pre-commit'));
        $this->assertTrue($restriction->isApplicableFor('pre-push'));
        $this->assertFalse($restriction->isApplicableFor('post-push'));
    }

    /**
     * Tests Restriction::isApplicable
     */
    public function testIsApplicableFromString(): void
    {
        $restriction = Restriction::fromString('pre-commit');

        $this->assertTrue($restriction->isApplicableFor('pre-commit'));
        $this->assertFalse($restriction->isApplicableFor('post-push'));
    }

    /**
     * Tests Restriction::isApplicable
     */
    public function testIsApplicableEmpty(): void
    {
        $restriction = Restriction::empty();

        $this->assertFalse($restriction->isApplicableFor('pre-commit'));
        $this->assertFalse($restriction->isApplicableFor('post-push'));
    }

    /**
     * Tests Restriction::isApplicable
     */
    public function testIsApplicableEmptyAddingRestrictions(): void
    {
        $restriction = Restriction::empty()->with('pre-commit');

        $this->assertTrue($restriction->isApplicableFor('pre-commit'));
        $this->assertFalse($restriction->isApplicableFor('post-push'));
    }

    /**
     * Tests Restriction::isApplicable
     */
    public function testIsApplicableWithAlreadyApplicableRestrictions(): void
    {
        $restriction = Restriction::fromString('pre-commit')->with('pre-commit');

        $this->assertTrue($restriction->isApplicableFor('pre-commit'));
        $this->assertFalse($restriction->isApplicableFor('post-push'));
    }
}
