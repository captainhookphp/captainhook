<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command\Placeholder;

use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Mockery as AppMockery;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    use IOMockery;
    use AppMockery;
    use ConfigMockery;

    /**
     * Tests Config::replacement
     */
    public function testConfigValue(): void
    {
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $config->expects($this->once())->method('getGitDirectory')->willReturn('./.git');

        $placeholder = new Config($io, $config, $repo);
        $gitDir      = $placeholder->replacement(['value-of' => 'git-directory']);

        $this->assertEquals('./.git', $gitDir);
    }

    /**
     * Tests Config::replacement
     */
    public function testCustomConfigValue(): void
    {
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $config->expects($this->once())->method('getCustomSettings')->willReturn(['foo' => 'bar']);

        $placeholder = new Config($io, $config, $repo);
        $replace     = $placeholder->replacement(['value-of' => 'custom>>foo']);

        $this->assertEquals('bar', $replace);
    }

    /**
     * Tests Config::replacement
     */
    public function testCustomConfigValueNotFound(): void
    {
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $config->expects($this->once())->method('getCustomSettings')->willReturn(['foo' => 'bar']);

        $placeholder = new Config($io, $config, $repo);
        $replace     = $placeholder->replacement(['value-of' => 'custom>>bar']);

        $this->assertEquals('', $replace);
    }

    /**
     * Tests Config::replacement
     */
    public function testNoValueOf(): void
    {
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Config($io, $config, $repo);
        $gitDir      = $placeholder->replacement([]);

        $this->assertEquals('', $gitDir);
    }

    /**
     * Tests Config::replacement
     */
    public function testInvalidConfigValue(): void
    {
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();

        $placeholder = new Config($io, $config, $repo);
        $gitDir      = $placeholder->replacement(['value-of' => 'includes']);

        $this->assertEquals('', $gitDir);
    }
}
