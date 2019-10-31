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
use Exception;
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
        $this->expectException(Exception::class);
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
     * Tests Config::getVendorDirectory
     */
    public function testGetVendorDirectoryDefault()
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path);

        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'vendor', $config->getVendorDirectory());
    }

    /**
     * Tests Config::getVendorDirectory
     */
    public function testGetVendorDirectorySetting()
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path, true, ['vendor-directory' => 'libs/composer']);

        $this->assertEquals(dirname($path) . DIRECTORY_SEPARATOR . 'libs/composer', $config->getVendorDirectory());
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
     * Tests Config::getRunMode
     */
    public function testGetRunMode()
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $this->assertEquals('docker', $config->getRunMode());
    }

    /**
     * Tests Config::getRunExec
     */
    public function testGetRunExec()
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $this->assertEquals('foo', $config->getRunExec());
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

    /**
     * Tests Config::getJsonData
     */
    public function testGetJsonDataWithoutEmptyConfig()
    {
        $config = new Config('foo.json', true, []);
        $json   = $config->getJsonData();

        $this->assertArrayNotHasKey('config', $json);
    }

    /**
     * Tests Config::getJsonData
     */
    public function testGetJsonDataWithConfigSection()
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $json   = $config->getJsonData();

        $this->assertIsArray($json);
        $this->assertIsArray($json['config']);
        $this->assertEquals('foo', $json['config']['run-exec']);
    }
}
