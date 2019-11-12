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
    public function testTemplateFirstParty() : void
    {
        $repoPath = realpath(__DIR__ . '/../../../files/storage');
        $template = new Docker($repoPath, 'does/not/matter', 'docker exec captain-container');
        $code = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
        $this->assertStringContainsString($repoPath . DIRECTORY_SEPARATOR . 'captainhook-run', $code);
    }

    /**
     * Tests Docker::getCode
     */
    public function testTemplateThirdParty() : void
    {
        $repoPath = realpath(__DIR__);

        $template = new Docker($repoPath, $repoPath, 'docker exec captain-container');
        $code = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec captain-container', $code);
        $this->assertStringContainsString($repoPath . '/bin/captainhook-run', $code);
    }
}
