<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App;

use Exception;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Tests Config::__construct
     */
    public function testConstructor(): void
    {
        $config = new Config('./no-config.json');
        $this->assertFalse($config->isLoadedFromFile());
    }

    /**
     * Tests Config::isLoadedFromFile
     */
    public function testIsLoadedFromFile(): void
    {
        $config = new Config('valid.json', true);
        $this->assertTrue($config->isLoadedFromFile());
    }

    /**
     * Tests Config::getHookConfig
     */
    public function testGetInvalidHook(): void
    {
        $this->expectException(Exception::class);
        $config = new Config('./no-config.json');
        $config->getHookConfig('foo');
    }

    /**
     * Tests Config::getGitDirectory
     */
    public function testAssumeCwdAsGitDir(): void
    {
        $config = new Config('./no-config.json');
        $this->assertEquals(getcwd() . '/.git', $config->getGitDirectory());
    }

    /**
     * Tests Config::getPath
     */
    public function testGetPath(): void
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path);

        $this->assertEquals($path, $config->getPath());
    }

    /**
     * Tests Config::getBootstrap
     */
    public function testGetBootstrapDefault(): void
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path);

        $this->assertEquals('vendor/autoload.php', $config->getBootstrap());
    }

    /**
     * Tests Config::getBootstrap
     */
    public function testGetBootstrapSetting(): void
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path, true, ['bootstrap' => 'libs/autoload.php']);

        $this->assertEquals('libs/autoload.php', $config->getBootstrap());
    }

    /**
     * Tests Config::useAnsiColors
     */
    public function testAnsiColorsEnabledByDefault(): void
    {
        $config = new Config('foo.json', true);
        $this->assertEquals(true, $config->useAnsiColors());
    }

    /**
     * Tests Config::useAnsiColors
     */
    public function testDisableAnsiColors(): void
    {
        $config = new Config('foo.json', true, ['ansi-colors' => false]);
        $this->assertEquals(false, $config->useAnsiColors());
    }

    /**
     * Tests Config::getRunMode
     */
    public function testGetRunMode(): void
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $this->assertEquals('docker', $config->getRunMode());
    }

    /**
     * Tests Config::getRunExec
     */
    public function testGetRunExec(): void
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $this->assertEquals('foo', $config->getRunExec());
    }

    /**
     * Tests Config::getRunPath
     */
    public function testGetRunPathEmptyByDefault(): void
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $this->assertEquals('', $config->getRunPath());
    }

    /**
     * Tests Config::getRunPath
     */
    public function testGetRunPath(): void
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo', 'run-path' => '/foo']);
        $this->assertEquals('/foo', $config->getRunPath());
    }

    /**
     * Tests Config::failOnFirstError default
     */
    public function testFailOnFirstErrorDefault(): void
    {
        $config = new Config('foo.json', true, []);
        $this->assertEquals(true, $config->failOnFirstError());
    }

    /**
     * Tests Config::failOnFirstError
     */
    public function testFailOnFirstError(): void
    {
        $config = new Config('foo.json', true, ['fail-on-first-error' => false]);
        $this->assertEquals(false, $config->failOnFirstError());
    }

    /**
     * Tests Config::getJsonData
     */
    public function testGetJsonData(): void
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
    public function testGetJsonDataWithoutEmptyConfig(): void
    {
        $config = new Config('foo.json', true, []);
        $json   = $config->getJsonData();

        $this->assertArrayNotHasKey('config', $json);
    }

    /**
     * Tests Config::getJsonData
     */
    public function testGetJsonDataWithConfigSection(): void
    {
        $config = new Config('foo.json', true, ['run-mode' => 'docker', 'run-exec' => 'foo']);
        $json   = $config->getJsonData();

        $this->assertIsArray($json);
        $this->assertIsArray($json['config']);
        $this->assertEquals('foo', $json['config']['run-exec']);
    }
}
