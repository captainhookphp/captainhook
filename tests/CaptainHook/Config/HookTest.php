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

class HookTest extends TestCase
{
    /**
     * Tests Hook::__construct
     */
    public function testDisabledByDefault()
    {
        $hook   = new Hook();
        $config = $hook->getJsonData();

        $this->assertFalse($hook->isEnabled());
        $this->assertFalse($config['enabled']);
    }

    /**
     * Tests Hook::setEnabled
     */
    public function testSetEnabled()
    {
        $hook   = new Hook();
        $hook->setEnabled(true);
        $config = $hook->getJsonData();

        $this->assertTrue($hook->isEnabled());
        $this->assertTrue($config['enabled']);
    }

    /**
     * Tests Hook::__construct
     */
    public function testEmptyActions()
    {
        $hook   = new Hook();
        $config = $hook->getJsonData();

        $this->assertCount(0, $hook->getActions());
        $this->assertCount(0, $config['actions']);
    }

    /**
     * Tests Hook::addAction
     */
    public function testAddAction()
    {
        $hook   = new Hook();
        $hook->addAction(new Action('php', '\\Foo\\Bar'));
        $config = $hook->getJsonData();

        $this->assertCount(1, $hook->getActions());
        $this->assertCount(1, $config['actions']);
    }
}
