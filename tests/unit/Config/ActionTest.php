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
    public function testItCanAllowFailure(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['allow-failure' => true]);
        $this->assertTrue($action->isFailureAllowed());
    }

    public function testItDoesNotAllowFailureByDefault(): void
    {
        // nothing configured so should not be allowed
        $action = new Action('\\Foo\\Bar', [], [], []);
        $this->assertFalse($action->isFailureAllowed());
    }

    public function testItAllowsChangingTheFailureDefault(): void
    {
        $action = new Action('\\Foo\\Bar');
        $this->assertTrue($action->isFailureAllowed(true));
    }

    public function testFailureCanBeExplicitlyDisallowed(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['allow-failure' => false]);
        $this->assertFalse($action->isFailureAllowed(true));
    }

    public function testItProvidesAccessToTheAction(): void
    {
        $action = new Action('\\Foo\\Bar');
        $this->assertEquals('\\Foo\\Bar', $action->getAction());
    }

    public function testItProvidesAccessToTheLabel(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['label' => 'My label']);
        $this->assertEquals('My label', $action->getLabel());
    }

    public function testTheLabelIsEmptyByDefault(): void
    {
        $action = new Action('\\Foo\\Bar');
        $this->assertEquals('\\Foo\\Bar', $action->getLabel());
    }

    public function testItProvidedAccessToTheOptions(): void
    {
        $action = new Action('\\Foo\\Bar');
        $this->assertEquals([], $action->getOptions()->getAll());
    }

    public function testThatOptionsAreEmptyByDefault(): void
    {
        $action = new Action('\\Foo\\Bar');
        $config = $action->getJsonData();
        $this->assertCount(1, $config);
    }

    /**
     * @throws \Exception
     */
    public function testConditionsGetExportedToJson(): void
    {
        $conditions = [
            ['exec' => '\\Foo\\Bar', 'args' => []]
        ];

        $action = new Action('\\Foo\\Bar', [], $conditions);
        $config = $action->getJsonData();
        $this->assertCount(1, $config['conditions']);
    }

    /**
     * @throws \Exception
     */
    public function testItProvidesAccessToConditions(): void
    {
        $conditions = [
            ['exec' => '\\Foo\\Bar', 'args' => []],
            ['exec' => '\\Fiz\\Baz', 'args' => []]
        ];

        $action = new Action('\\Foo\\Bar', [], $conditions);
        $this->assertCount(2, $action->getConditions());
    }

    public function testSettingsWillBeExportedToJson(): void
    {
        $action = new Action('\\Foo\\Bar', [], [], ['allow-failure' => true]);
        $config = $action->getJsonData();
        $this->assertCount(1, $config['config']);
    }
}
