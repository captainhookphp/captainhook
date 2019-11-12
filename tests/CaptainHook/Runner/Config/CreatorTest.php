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

class CreatorTest extends BaseTestRunner
{
    /**
     * Tests Creator::run
     */
    public function testConfigureFileExists(): void
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $runner = new Creator($io, $config);
        $config->expects($this->once())->method('isLoadedFromFile')->willReturn(true);
        $io->method('ask')->will($this->onConsecutiveCalls('y', 'y', '\\Foo\\Bar', 'y', 'n'));
        $runner->advanced(true)
               ->run();

    }

    /**
     * Tests Creator::run
     */
    public function testConfigureFileExtend(): void
    {
        $path   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(__FILE__);
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $runner = new Creator($io, $config);
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
