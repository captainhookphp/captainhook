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

class InstallerTest extends BaseTestRunner
{
    /**
     * Tests Installer::setHook
     */
    public function testSetHookInvalid()
    {
        $this->expectException(\Exception::class);

        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Installer($io, $config, $repo);
        $runner->setHook('iDoNotExist');
    }

    /**
     * Tests Installer::installHook
     */
    public function testInstallHook()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Installer($io, $config, $repo);
        $io->expects($this->once())->method('ask')->willReturn('no');
        $runner->installHook('pre-push', true);
    }


    /**
     * Tests Installer::installHook
     */
    public function testWriteHookFile()
    {
        $io     = $this->getIOMock();
        $config = $this->getConfigMock();
        $repo   = $this->getRepositoryMock();
        $runner = new Installer($io, $config, $repo);
        $repo->expects($this->once())->method('hookExists')->willReturn(true);
        $io->expects($this->once())->method('ask')->willReturn('no');
        $runner->writeHookFile('pre-push');
    }
}
