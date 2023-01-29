<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Config;

use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    /**
     * Tests Action::getJsonData
     */
    public function testAllowFailure(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['allow-failure' => true]);
        $this->assertTrue($action->isFailureAllowed());
    }

    /**
     * Tests Action::getJsonData
     */
    public function testFailureNotAllowedByDefault(): void
    {
        // nothing configured so should not be allowed
        $action = new Action('\\Foo\\Bar', [], [], []);
        $this->assertFalse($action->isFailureAllowed());
    }

    /**
     * Tests Action::getJsonData
     */
    public function testFailureAllowedByDefault(): void
    {
        $action = new Action('\\Foo\\Bar');
        $this->assertTrue($action->isFailureAllowed(true));
    }

    /**
     * Tests Action::getJsonData
     */
    public function testFailureExcplitlyNotAllowed(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['allow-failure' => false]);
        $this->assertFalse($action->isFailureAllowed(true));
    }

    /**
     * Tests Action::getAction
     */
    public function testGetAction(): void
    {
        $action = new Action('\\Foo\\Bar');

        $this->assertEquals('\\Foo\\Bar', $action->getAction());
    }

    /**
     * Tests Action::getOptions
     */
    public function testGetOptions(): void
    {
        $action = new Action('\\Foo\\Bar');

        $this->assertEquals([], $action->getOptions()->getAll());
    }

    /**
     * Tests Action::getJsonData
     */
    public function testEmptyOptions(): void
    {
        $action = new Action('\\Foo\\Bar');
        $config = $action->getJsonData();

        $this->assertCount(0, $config['options']);
    }

    /**
     * Tests Action::getJsonData
     *
     * @throws \Exception
     */
    public function testConditions(): void
    {
        $conditions = [
            ['exec' => '\\Foo\\Bar', 'args' => []]
        ];

        $action = new Action('\\Foo\\Bar', [], $conditions);
        $config = $action->getJsonData();

        $this->assertCount(1, $config['conditions']);
    }

    /**
     * Tests Action::getConditions
     *
     * @throws \Exception
     */
    public function testGetConditions(): void
    {
        $conditions = [
            ['exec' => '\\Foo\\Bar', 'args' => []],
            ['exec' => '\\Fiz\\Baz', 'args' => []]
        ];

        $action = new Action('\\Foo\\Bar', [], $conditions);

        $this->assertCount(2, $action->getConditions());
    }

    /**
     * Tests Action::getJsonData
     */
    public function testSettings(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['allow-failure' => true]);
        $config = $action->getJsonData();

        $this->assertCount(1, $config['settings']);
    }
}
