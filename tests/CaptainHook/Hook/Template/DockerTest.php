<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Template;

use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Camino\Path\Directory;

class DockerTest extends TestCase
{
    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookDevelopment() : void
    {
        $repoPath = new Directory(realpath(__DIR__ . '/../../../files/template-ch'));
        $template = new Docker($repoPath, new Directory('/does/not/matter'), 'docker exec cap-container', '');
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec cap-container', $code);
        $this->assertStringContainsString('./captainhook-run', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookAsLibrary() : void
    {
        $repo     = new Directory(realpath(__DIR__));
        $template = new Docker($repo, new Directory($repo->getPath() . '/vendor'), 'docker exec cap-container', '');
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec cap-container', $code);
        $this->assertStringContainsString('./vendor/bin/captainhook-run', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCustomPath() : void
    {
        $repoPath = new Directory(realpath(__DIR__ . '/../../../files/template-ch'));
        $template = new Docker($repoPath, new Directory('/does/not/matter'), 'docker exec cap-container', './foo');
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec cap-container', $code);
        $this->assertStringContainsString('./foo/captainhook-run', $code);
    }
}
