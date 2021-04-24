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

use CaptainHook\App\Exception\InvalidPlugin;
use CaptainHook\App\Plugin\CaptainHook as CaptainHookPlugin;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    /**
     * @var string
     */
    private $class;

    protected function setUp(): void
    {
        $plugin = new class implements CaptainHookPlugin {
        };
        $this->class = get_class($plugin);
    }

    public function testGetPluginClass(): void
    {
        $plugin = new Plugin($this->class);

        $this->assertEquals($this->class, $plugin->getPluginClass());
    }

    public function testGetPlugin(): void
    {
        $plugin = new Plugin($this->class);

        $this->assertInstanceOf($this->class, $plugin->getPlugin());
    }

    public function testGetOptions(): void
    {
        $plugin = new Plugin($this->class);

        $this->assertEquals([], $plugin->getOptions()->getAll());
    }

    public function testEmptyOptions(): void
    {
        $plugin = new Plugin($this->class);
        $config = $plugin->getJsonData();

        $this->assertCount(0, $config['options']);
    }

    public function testPopulatedOptions(): void
    {
        $plugin = new Plugin($this->class, ['foo' => 'bar']);
        $config = $plugin->getJsonData();

        $this->assertSame(['foo' => 'bar'], $config['options']);
    }

    public function testThrowsExceptionForInvalidPlugin(): void
    {
        $this->expectException(InvalidPlugin::class);
        $this->expectExceptionMessage('\\Foo\\Bar is not a valid CaptainHook plugin.');

        new Plugin('\\Foo\\Bar');
    }
}
