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
    public function testSetInvalidHook(): void
    {
        $this->expectException(InvalidHookName::class);

        $io       = $this->createIOMock();
        $config   = $this->createConfigMock();
        $repo     = $this->createRepositoryMock();
        $template = $this->createTemplateMock();

        $runner = new Installer($io, $config, $repo, $template);
        $runner->setHook('itDoNotExist');
    }

    /**
     * Tests Installer::run
     */
    public function testHookInstallationDeclined(): void
    {
        $fakeRepo = new DummyRepo();

        $io       = $this->createIOMock();
        $config   = $this->createConfigMock();
        $repo     = $this->createRepositoryMock($fakeRepo->getRoot());
        $template = $this->createTemplateMock();

        $io->expects($this->atLeast(5))->method('ask')->willReturn('n');

        $runner = new Installer($io, $config, $repo, $template);
        $runner->run();

        $this->assertFileNotExists($fakeRepo->getHookDir() . '/pre-commit');
        $this->assertFileNotExists($fakeRepo->getHookDir() . '/pre-push');
    }

    /**
     * Tests Installer::run
     */
    public function testWriteHook(): void
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

        $runner = new Installer($io, $config, $repo, $template);
        $runner->setHook('pre-commit');
        $runner->run('pre-commit');

        $this->assertFileExists($fakeRepo->getHookDir() . '/pre-commit');
    }

    /**
     * Tests Installer::writeHookFile
     */
    public function testSkipExisting(): void
    {
        $io       = $this->createIOMock();
        $config   = $this->createConfigMock();
        $repo     = $this->createRepositoryMock();
        $template = $this->createTemplateMock();

        $io->expects($this->atLeast(1))->method('write');
        $repo->expects($this->once())->method('hookExists')->willReturn(true);

        $runner = new Installer($io, $config, $repo, $template);
        $runner->setSkipExisting(true);
        $runner->setHook('pre-commit');
        $runner->run();
    }

    /**
     * Tests Installer::writeHookFile
     */
    public function testDeclineOverwrite(): void
    {
        $io       = $this->createIOMock();
        $config   = $this->createConfigMock();
        $repo     = $this->createRepositoryMock();
        $template = $this->createTemplateMock();

        $io->expects($this->once())->method('ask')->willReturn('n');
        $repo->expects($this->once())->method('hookExists')->willReturn(true);

        $runner = new Installer($io, $config, $repo, $template);
        $runner->setHook('pre-commit');
        $runner->run();
    }
}
