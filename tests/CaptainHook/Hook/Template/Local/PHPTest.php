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

use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

class PHPTest extends TestCase
{
    /**
     * Tests PHP::getCode
     */
    public function testSrcTemplate(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $bootstrap  = new File('/foo/bar/vendor/autoload.php');
        $executable = new File('/foo/bar/vendor/bin/captainhook');
        $template   = new PHP($repo, $config, $executable, $bootstrap, false);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('$captainHook->run(', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testSrcTemplateExtExecutable(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $bootstrap  = new File('/foo/bar/vendor/autoload.php');
        $executable = new File('/usr/local/bin/captainhook');
        $template   = new PHP($repo, $config, $executable, $bootstrap, false);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('$captainHook->run(', $code);
    }

    /**
     * Tests PHP::getCode
     */
    public function testPharTemplate(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $bootstrap  = new File('/foo/bar/vendor/autoload.php');
        $executable = new File('/foo/bar/tools/captainhook.phar');
        $template   = new PHP($repo, $config, $executable, $bootstrap, true);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('hook:commit-msg', $code);
        $this->assertStringContainsString('tools/captainhook.phar', $code);
    }
}
