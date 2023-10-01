<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Template\Local;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Hook\Template\PathInfo;
use PHPUnit\Framework\TestCase;

class ShellTest extends TestCase
{
    use ConfigMockery;

    /**
     * Tests Shell::getCode
     */
    public function testTemplate(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');
        $config->method('getPhpPath')->willReturn('');

        $template = new Shell($pathInfo, $config, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringNotContainsString('php7.4', $code);
        $this->assertStringContainsString('vendor/bin/captainhook $INTERACTIVE', $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateWithDefinedPHP(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');
        $config->method('getPhpPath')->willReturn('/usr/bin/php7.4');

        $template = new Shell($pathInfo, $config, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/bin/php7.4', $code);
        $this->assertStringContainsString('vendor/bin/captainhook $INTERACTIVE', $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateWithDefinedPHPAndRunPath(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');
        $config->method('getPhpPath')->willReturn('/usr/bin/php7.4');
        $config->method('getRunPath')->willReturn('tools/captainhook.phar');

        $template = new Shell($pathInfo, $config, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/bin/php7.4', $code);
        $this->assertStringContainsString('tools/captainhook.phar $INTERACTIVE', $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateExtExecutable(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('/usr/local/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');
        $config->method('getPhpPath')->willReturn('');

        $template = new Shell($pathInfo, $config, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringNotContainsString('php7.4', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringNotContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateExtExecutableWithDefinedPHP(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('/usr/local/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');
        $config->method('getPhpPath')->willReturn('/usr/bin/php7.4');

        $template = new Shell($pathInfo, $config, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);

        $this->assertStringContainsString('/usr/bin/php7.4', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringNotContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateExtExecutableWithUserInput(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('/usr/local/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');
        $config->method('getPhpPath')->willReturn('');

        $template = new Shell($pathInfo, $config, false);
        $code     = $template->getCode('prepare-commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Returns the expected TTY redirection lines
     *
     * @return string
     */
    private function getTtyRedirectionLines(): string
    {
        return <<<'EOD'
if [ -t 1 ]; then
    # If we're in a terminal, redirect stdout and stderr to /dev/tty and
    # read stdin from /dev/tty. Allow interactive mode for CaptainHook.
    exec >/dev/tty 2>/dev/tty </dev/tty
    INTERACTIVE=""
fi
EOD;
    }
}
