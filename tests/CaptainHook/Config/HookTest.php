<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Config;

class HookTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Hook::__construct
     */
    public function testDisabledByDefault()
    {
        $hook   = new Hook();
        $config = $hook->getJsonData();

        $this->assertEquals(false, $hook->isEnabled());
        $this->assertEquals(false, $config['enabled']);
    }

    /**
     * Tests Hook::setEnabled
     */
    public function testSetEnabled()
    {
        $hook   = new Hook();
        $hook->setEnabled(true);
        $config = $hook->getJsonData();

        $this->assertEquals(true, $hook->isEnabled());
        $this->assertEquals(true, $config['enabled']);
    }

    /**
     * Tests Hook::__construct
     */
    public function testEmptyActions()
    {
        $hook   = new Hook();
        $config = $hook->getJsonData();

        $this->assertEquals(0, count($hook->getActions()));
        $this->assertEquals(0, count($config['actions']));
    }

    /**
     * Tests Hook::addAction
     */
    public function testAddAction()
    {
        $hook   = new Hook();
        $hook->addAction(new Action('php', '\\Foo\\Bar'));
        $config = $hook->getJsonData();

        $this->assertEquals(1, count($hook->getActions()));
        $this->assertEquals(1, count($config['actions']));
    }
}
