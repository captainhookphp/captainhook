<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Config;

use CaptainHook\App\Runner\BaseTestRunner;

class EditorTest extends BaseTestRunner
{
    /**
     * Tests Editor::run
     */
    public function testInvalidHook(): void
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();

        $runner = new Editor($io, $config);
        $runner->setHook('foo')
               ->setChange('EnableHook')
               ->run();
    }

    /**
     * Tests Editor::run
     */
    public function testNoHook(): void
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->expects($this->once())->method('isLoadedFromFile')->willReturn(true);

        $runner = new Editor($io, $config);
        $runner->setChange('EnableHook')
               ->run();
    }

    /**
     * Tests Editor::run
     */
    public function testNoChange(): void
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->expects($this->once())->method('isLoadedFromFile')->willReturn(true);

        $runner = new Editor($io, $config);
        $runner->setHook('pre-commit')
               ->run();
    }

    /**
     * Tests Editor::run
     */
    public function testInvalidChange(): void
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->expects($this->once())->method('isLoadedFromFile')->willReturn(true);

        $runner = new Editor($io, $config);
        $runner->setChange('InvalidChange')
               ->setHook('pre-commit')
               ->run();
    }

    /**
     * Tests Editor::run
     */
    public function testNoConfiguration()
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $config->expects($this->once())->method('isLoadedFromFile')->willReturn(false);

        $runner = new Editor($io, $config);
        $runner->setChange('AddAction')
               ->setHook('pre-commit')
               ->run();
    }

    /**
     * Tests Editor::run
     */
    public function __testConfigureFileExtend()
    {
        $path   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__);
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Creator($io, $config, $repo);
        $config->method('getHookConfig')->willReturn($this->getHookConfigMock());
        $config->method('getPath')->willReturn($path);
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', '\\Foo\\Bar', 'y', 'n'));
        $io->expects($this->once())->method('askAndValidate')->willReturn('foo:bar');
        $runner->extend(true)
               ->advanced(true)
               ->run();

        $this->assertFileExists($path);
        unlink($path);
    }
}
