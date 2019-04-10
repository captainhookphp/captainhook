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

use CaptainHook\App\Config;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * Tests Factory::create
     */
    public function testCreate()
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid.json'));

        $this->assertInstanceOf(Config::class, $config);
        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithConditions()
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid-with-conditions.json'));

        $this->assertInstanceOf(Config::class, $config);
        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
    }
}
