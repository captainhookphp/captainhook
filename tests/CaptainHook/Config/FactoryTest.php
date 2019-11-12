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
use Exception;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * Tests Factory::create
     */
    public function testCreate() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid.json'));

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithAbsoluteGitDir() : void
    {
        $config = Factory::create(
            realpath(__DIR__ . '/../../files/config/valid.json'),
            ['git-directory' => '/foo']
        );

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
        $this->assertEquals('/foo', $config->getGitDirectory());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithRelativeGitDir() : void
    {
        $path   = realpath(__DIR__ . '/../../files/config/valid.json');
        $config = Factory::create($path, ['git-directory' => '../.git']);

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
        $this->assertEquals(\dirname($path) . DIRECTORY_SEPARATOR . '../.git', $config->getGitDirectory());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithConditions() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid-with-conditions.json'));

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithAllSetting() : void
    {
        $path   = realpath(__DIR__ . '/../../files/config/valid-with-all-settings.json');
        $gitDir = \dirname($path) .DIRECTORY_SEPARATOR . '../../../.git';
        $config = Factory::create($path);

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
        $this->assertEquals('verbose', $config->getVerbosity());
        $this->assertEquals($gitDir, $config->getGitDirectory());
        $this->assertEquals(false, $config->useAnsiColors());
        $this->assertEquals('docker', $config->getRunMode());
        $this->assertEquals('docker exec CONTAINER_NAME', $config->getRunExec());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithIncludes() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid-with-includes.json'));

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(2, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithValidNestedIncludes() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid-with-nested-includes.json'));

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(3, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithInvalidNestedIncludes() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/invalid-with-nested-includes.json'));

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(2, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testCreateWithInvalidIncludes() : void
    {
        $this->expectException(Exception::class);
        Factory::create(realpath(__DIR__ . '/../../files/config/valid-with-invalid-includes.json'));
    }

    /**
     * Tests Factory::create
     */
    public function testCreateEmptyWithIncludes() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/empty-with-includes.json'));

        $this->assertTrue($config->getHookConfig('pre-commit')->isEnabled());
        $this->assertCount(1, $config->getHookConfig('pre-commit')->getActions());
    }

    /**
     * Tests Factory::create
     */
    public function testWithMainConfigurationOverridingInclude() : void
    {
        $config = Factory::create(realpath(__DIR__ . '/../../files/config/valid-with-disabled-action.json'));

        $this->assertFalse($config->getHookConfig('pre-commit')->isEnabled());
    }
}
