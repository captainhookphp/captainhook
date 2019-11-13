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

class DockerTest extends TestCase
{
    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainhookDevelopment() : void
    {
        $repoPath = realpath(__DIR__ . '/../../../files/template-ch');
        $template = new Docker($repoPath, $repoPath . '/does/not/matter', 'docker exec captain-container');
        $code = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
        $this->assertStringContainsString('./captainhook-run', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateCaptainHookAsLibrary() : void
    {
        $repoPath = realpath(__DIR__);

        $template = new Docker($repoPath, $repoPath . '/vendor', 'docker exec captain-container');
        $code = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
        $this->assertStringContainsString('./vendor/bin/captainhook-run', $code);
    }
}
