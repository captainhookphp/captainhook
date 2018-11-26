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

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests Config::__construct
     */
    public function testConstructor()
    {
        $config = new Config('./no-config.json');

        $this->assertTrue($config->getHookConfig('commit-msg') instanceof Config\Hook);
        $this->assertTrue($config->getHookConfig('pre-commit') instanceof Config\Hook);
        $this->assertTrue($config->getHookConfig('pre-push') instanceof Config\Hook);

        $this->assertFalse($config->isLoadedFromFile());
    }

    /**
     * Tests Config::isLoadedFromFile
     */
    public function testIsLoadedFromFile()
    {
        $path   = realpath(__DIR__ . '/../files/config/valid.json');
        $config = new Config($path, true);

        $this->assertTrue($config->isLoadedFromFile());
    }

    /**
     * Tests Config::getHookConfig
     *
     * @expectedException \Exception
     */
    public function testGetInvalidHook()
    {
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
     * Tests Config::getJsonData
     */
    public function testGetJsonData()
    {
        $config = new Config('./no-config.json');
        $json   = $config->getJsonData();

        $this->assertTrue(is_array($json));
        $this->assertTrue(is_array($json['pre-commit']));
        $this->assertTrue(is_array($json['commit-msg']));
        $this->assertTrue(is_array($json['pre-push']));
    }
}
