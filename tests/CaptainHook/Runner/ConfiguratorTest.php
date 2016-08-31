<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

class ConfiguratorTest extends BaseTestRunner
{
    /**
     * Tests Installer::installHook
     */
    public function testConfigureCliHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Configurator($io, $config, $repo);
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', 'echo \'foo\'', 'n'));
        $runner->configureHook($config, 'pre-push', true);
    }

    /**
     * Tests Installer::installHook
     */
    public function testConfigurePHPHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Configurator($io, $config, $repo);
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', '\\Foo\\Bar', 'y', 'n'));
        $io->expects($this->once())->method('askAndValidate')->willReturn('foo:bar');
        $runner->configureHook($config, 'pre-push', true);
    }

    /**
     * Tests Installer::installHook
     *
     * @expectedException \Exception
     */
    public function testConfigureFileExists()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Configurator($io, $config, $repo);
        $config->expects($this->once())->method('isLoadedFromFile')->willReturn(true);
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', '\\Foo\\Bar', 'y', 'n'));
        $runner->run();

    }


    /**
     * Tests Installer::installHook
     */
    public function testConfigureFileExtend()
    {
        $path   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__);
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Configurator($io, $config, $repo);
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $config->method('getPath')->willReturn($path);
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', '\\Foo\\Bar', 'y', 'n'));
        $io->expects($this->once())->method('askAndValidate')->willReturn('foo:bar');
        $runner->extend(true);
        $runner->run();

        $this->assertTrue(file_exists($path));
        unlink($path);
    }
}
