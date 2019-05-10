<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook;

use CaptainHook\App\Hook\Template\Docker;
use PHPUnit\Framework\TestCase;

class DockerTest extends TestCase
{
    /**
     * Tests Docker::getCode
     */
    public function testTemplate() : void
    {
        $template = new Docker('/foo/bar', '/foo/bar/vendor', 'captain-container');
        $code = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env bash', $code);
        $this->assertStringContainsString('docker exec', $code);
        $this->assertStringContainsString('captain-container', $code);
        $this->assertStringContainsString('./captainhook-run', $code);
    }
}
