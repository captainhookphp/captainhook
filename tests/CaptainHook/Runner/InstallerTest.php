<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Exception\InvalidHookName;
use CaptainHook\App\Git\DummyRepo;
use CaptainHook\App\Hook\Mockery as HookMockery;
use CaptainHook\App\Mockery as CHMockery;
use PHPUnit\Framework\TestCase;

class InstallerTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;
    use HookMockery;

    /**
     * Tests Installer::setHook
     *
     * @throws \CaptainHook\App\Exception\InvalidHookName
     */
    public function testSetHookInvalid(): void
    {
        $this->expectException(InvalidHookName::class);

        $io     = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();
        $runner = new Installer($io, $config, $repo);
        $runner->setHook('iDoNotExist');
    }

    /**
     * Tests Installer::installHook
     */
    public function testInstallHook(): void
    {
        $io     = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();
        $runner = new Installer($io, $config, $repo);
        $io->expects($this->once())->method('ask')->willReturn('no');
        $runner->installHook('pre-push', true);
    }


    /**
     * Tests Installer::installHook
     */
    public function testWriteHookFile(): void
    {
        $io     = $this->createIOMock();
        $config = $this->createConfigMock();
        $repo   = $this->createRepositoryMock();
        $runner = new Installer($io, $config, $repo);
        $repo->expects($this->once())->method('hookExists')->willReturn(true);
        $io->expects($this->once())->method('ask')->willReturn('no');
        $runner->writeHookFile('pre-push');
    }

    /**
     * Tests Installer::writeHookFile
     */
    public function testTemplate(): void
    {
        $fakeRepo = new DummyRepo();

        $io       = $this->createIOMock();
        $config   = $this->createConfigMock();
        $repo     = $this->createRepositoryMock($fakeRepo->getRoot());
        $template = $this->createTemplateMock();

        $template->expects($this->once())
                 ->method('getCode')
                 ->with('pre-commit')
                 ->willReturn('');

        $runner = new Installer($io, $config, $repo);
        $runner->setTemplate($template);
        $runner->writeHookFile('pre-commit');

        $this->assertFileExists($fakeRepo->getHookDir() . '/pre-commit');
    }
}
