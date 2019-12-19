<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Hook\Template\Docker\Config as DockerConfig;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

class DockerTest extends TestCase
{
    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookDevelopment(): void
    {
        $repo       = new Directory(realpath(__DIR__ . '/../../../files/template-ch'));
        $executable = new File($repo->getPath() . '/does/not/matter');
        $config     = new File($repo->getPath() . '/captainhook.json');
        $docker     = new DockerConfig('docker exec cap-container', '');

        $template = new Docker($repo, $config, $executable, $docker, 'vendor/autoload.php');
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('docker exec cap-container', $code);
        $this->assertStringContainsString('./bin/captainhook', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookAsLibrary(): void
    {
        $repo       = new Directory(realpath(__DIR__));
        $executable = new File($repo->getPath() . '/vendor/bin/captainhook');
        $config     = new File($repo->getPath() . '/captainhook.json');
        $docker     = new DockerConfig('docker exec cap-container', '');

        $template   = new Docker($repo, $config, $executable, $docker, 'vendor/autoload.php');
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('docker exec cap-container', $code);
        $this->assertStringContainsString('./vendor/bin/captainhook', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCustomPath(): void
    {
        $repo       = new Directory(realpath(__DIR__ . '/../../../files/template-ch'));
        $executable = new File($repo->getPath() . '/does/not/matter');
        $config     = new File($repo->getPath() . '/captainhook.json');
        $docker     = new DockerConfig('docker exec cap-container', './foo/captainhook');

        $template = new Docker($repo, $config, $executable, $docker, 'vendor/autoload.php');
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('docker exec cap-container', $code);
        $this->assertStringContainsString('./foo/captainhook', $code);
    }
}
