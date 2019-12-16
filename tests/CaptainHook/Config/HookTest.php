<?php

/**
 * This file is part of CaptainHook
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
    public function testDisabledByDefault(): void
    {
        $hook   = new Hook('pre-commit');
        $config = $hook->getJsonData();

        $this->assertFalse($hook->isEnabled());
        $this->assertFalse($config['enabled']);
    }

    /**
     * Tests Hook::setEnabled
     */
    public function testSetEnabled(): void
    {
        $hook   = new Hook('pre-commit');
        $hook->setEnabled(true);
        $config = $hook->getJsonData();

        $this->assertTrue($hook->isEnabled());
        $this->assertTrue($config['enabled']);
    }

    /**
     * Tests Hook::__construct
     */
    public function testEmptyActions(): void
    {
        $hook   = new Hook('pre-commit');
        $config = $hook->getJsonData();

        $this->assertCount(0, $hook->getActions());
        $this->assertCount(0, $config['actions']);
    }

    /**
     * Tests Hook::addAction
     */
    public function testAddAction(): void
    {
        $hook   = new Hook('pre-commit');
        $hook->addAction(new Action('\\Foo\\Bar'));
        $config = $hook->getJsonData();

        $this->assertCount(1, $hook->getActions());
        $this->assertCount(1, $config['actions']);
    }
}
