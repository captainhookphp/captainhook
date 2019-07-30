<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App;

use CaptainHook\App\Config\Hook;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Tests Config::__construct
     */
    public function testConstructor()
    {
        $config = new Config('./no-config.json');

        $this->assertInstanceOf(Hook::class, $config->getHookConfig('commit-msg'));
        $this->assertInstanceOf(Hook::class, $config->getHookConfig('pre-commit'));
        $this->assertInstanceOf(Hook::class, $config->getHookConfig('pre-push'));

        $this->assertFalse($config->isLoadedFromFile());
    }

    /**
     * Tests Config::isLoadedFromFile
     */
    public function testIsLoadedFromFile()
    {
        $config = new Config('valid.json', true);
        $this->assertTrue($config->isLoadedFromFile());
    }

    /**
     * Tests Config::getHookConfig
     */
    public function testGetInvalidHook()
    {
        $this->expectException(\Exception::class);
        $config = new Config('./no-config.json');
        $config->getHookConfig('foo');
    }

    /**
     * Tests Config::getPath
     */
    public function testGetPath()
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path);

        $this->assertEquals($path, $config->getPath());
    }

    /**
     * Tests Config::useAnsiColors
     */
    public function testAnsiColorsEnabledByDefault()
    {
        $config = new Config('foo.json', true);
        $this->assertEquals(true, $config->useAnsiColors());
    }

    /**
     * Tests Config::useAnsiColors
     */
    public function testDisableAnsiColors()
    {
        $config = new Config('foo.json', true, ['ansi-colors' => false]);
        $this->assertEquals(false, $config->useAnsiColors());
    }

    /**
     * Tests Config::getJsonData
     */
    public function testGetJsonData()
    {
        $config = new Config('./no-config.json');
        $json   = $config->getJsonData();

        $this->assertIsArray($json);
        $this->assertIsArray($json['pre-commit']);
        $this->assertIsArray($json['commit-msg']);
        $this->assertIsArray($json['pre-push']);
    }
}
