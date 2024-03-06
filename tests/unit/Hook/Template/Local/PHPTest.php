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

class PHPTest extends TestCase
{
    use ConfigMockery;

    /**
     * Tests PHP::getCode
     */
    public function testSrcTemplate(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('./vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template = new PHP($pathInfo, $config, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('$captainHook->run(', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testSrcStdInHook(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('./vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template = new PHP($pathInfo, $config, false);
        $code     = $template->getCode('pre-push');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('STDIN', $code);
        $this->assertStringContainsString('$captainHook->run(', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testSrcTemplateExtExecutable(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('./vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template   = new PHP($pathInfo, $config, false);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('$captainHook->run(', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testPharAbsoluteExecutablePath(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('/usr/local/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template   = new PHP($pathInfo, $config, true);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testPharTemplate(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('tools/captainhook.phar');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template   = new PHP($pathInfo, $config, true);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('hook:commit-msg', $code);
        $this->assertStringContainsString('tools/captainhook.phar', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testPharStdIn(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('tools/captainhook.phar');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $config = $this->createConfigMock(false, 'captainhook.json');
        $config->method('getBootstrap')->willReturn('vendor/autoload.php');

        $template   = new PHP($pathInfo, $config, true);
        $code       = $template->getCode('post-rewrite');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('hook:post-rewrite', $code);
        $this->assertStringContainsString('STDIN', $code);
    }
}
