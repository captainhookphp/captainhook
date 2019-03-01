<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Config;

use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    /**
     * Tests Action::getType
     */
    public function testGetType()
    {
        $action = new Action('php', '\\Foo\\Bar');

        $this->assertEquals('php', $action->getType());
    }

    /**
     * Tests Action::getAction
     */
    public function testGetAction()
    {
        $action = new Action('php', '\\Foo\\Bar');

        $this->assertEquals('\\Foo\\Bar', $action->getAction());
    }

    /**
     * Tests Action::getOption
     */
    public function testGetOptions()
    {
        $action = new Action('php', '\\Foo\\Bar');

        $this->assertEquals([], $action->getOptions()->getAll());
    }

    /**
     * Tests Action::__construct
     */
    public function testEmptyOptions()
    {
        $action = new Action('php', '\\Foo\\Bar');
        $config = $action->getJsonData();

        $this->assertCount(0, $config['options']);
    }

    /**
     * Tests Action::__construct
     */
    public function testInvalidType()
    {
        $this->expectException(\Exception::class);
        $action = new Action('crap', 'with cinnamon and sugar');
    }
}
