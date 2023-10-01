<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Template;

use PHPUnit\Framework\TestCase;
use CaptainHook\App\Config\Mockery as ConfigMockery;

class DockerTest extends TestCase
{
    use ConfigMockery;

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookDevelopment(): void
    {
        $repo       = realpath(__DIR__ . '/../../../files/template-ch');
        $config     = $repo . '/captainhook.json';
        $executable = $repo . '/does/not/matter';
        $pathInfo   = new PathInfo($repo, $config, $executable);

        $configMock = $this->createConfigMock(false, $repo . '/captainhook.json');
        $configMock->method('getBootstrap')->willReturn('vendor/autoload.php');
        $configMock->method('getRunExec')->willReturn('docker exec cap-container');

        $template   = new Docker($pathInfo, $configMock);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('docker exec -i -e GIT_INDEX_FILE cap-container', $code);
        $this->assertStringContainsString('./bin/captainhook', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookAsLibrary(): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('./vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $configMock = $this->createConfigMock(false, 'captainhook.json');
        $configMock->method('getBootstrap')->willReturn('');
        $configMock->method('getRunExec')->willReturn('docker exec cap-container');


        $template = new Docker($pathInfo, $configMock);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('docker exec -i -e GIT_INDEX_FILE cap-container', $code);
        $this->assertStringContainsString('./vendor/bin/captainhook', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCustomPath(): void
    {
        $repo       = realpath(__DIR__ . '/../../../files/template-ch');
        $executable = $repo . '/does/not/matter';
        $config     = $repo . '/captainhook.json';
        $pathInfo   = new PathInfo($repo, $config, $executable);

        $configMock = $this->createConfigMock(false, $repo . '/captainhook.json');
        $configMock->method('getBootstrap')->willReturn('vendor/autoload.php');
        $configMock->method('getRunExec')->willReturn('docker exec cap-container');
        $configMock->method('getRunPath')->willReturn('./foo/captainhook');

        $template = new Docker($pathInfo, $configMock);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('docker exec -i -e GIT_INDEX_FILE cap-container', $code);
        $this->assertStringContainsString('./foo/captainhook', $code);
        $this->assertStringContainsString('bootstrap=vendor/autoload.php', $code);
    }

    /**
     * Tests Docker::getCode
     *
     * @dataProvider replacementPossibilities
     */
    public function testDockerCommandOptimization(string $exec, string $expected, string $msg): void
    {
        $pathInfo = $this->createMock(PathInfo::class);
        $pathInfo->method('getExecutablePath')->willReturn('./vendor/bin/captainhook');
        $pathInfo->method('getConfigPath')->willReturn('captainhook.json');

        $configMock = $this->createConfigMock(false, 'captainhook.json');
        $configMock->method('getBootstrap')->willReturn('');
        $configMock->method('getRunExec')->willReturn('docker exec ' . $exec);
        $configMock->method('getRunPath')->willReturn('/usr/local/bin/captainhook');

        $template = new Docker($pathInfo, $configMock);
        $code     = $template->getCode('prepare-commit-msg');

        $this->assertStringContainsString('docker exec ' . $expected, $code, $msg);
        $this->assertStringContainsString('/usr/local/bin/captainhook', $code);
    }

    /**
     * The testDockerCommandOptimization data provider
     *
     * @return array
     */
    public function replacementPossibilities(): array
    {
        return [
            ['cap-container', '-i -e GIT_INDEX_FILE cap-container', 'none'],
            ['-it cap-container', '-e GIT_INDEX_FILE -it cap-container', '-it'],
            ['-ti cap-container', '-e GIT_INDEX_FILE -ti cap-container', '-ti'],
            ['--interactive --tty cap-container', '-e GIT_INDEX_FILE --interactive --tty cap-container', 'long it'],
            ['--tty --interactive cap-container', '-e GIT_INDEX_FILE --tty --interactive cap-container', 'long ti'],
            ['--tty cap-container', '-i -e GIT_INDEX_FILE --tty cap-container', 'no i'],
            ['-xit cap-container', '-e GIT_INDEX_FILE -xit cap-container', 'prefixed i'],
            ['-xite=GIT_INDEX_FILE cap-container', '-xite=GIT_INDEX_FILE cap-container', 'prefixed e'],
            ['--env=GIT_INDEX_FILE cap-container', '-i --env=GIT_INDEX_FILE cap-container', 'long e'],
        ];
    }
}
