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

use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

class LocalTest extends TestCase
{
    /**
     * Tests Local::getCode
     */
    public function testSrcTemplate(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $bootstrap  = new File('/foo/bar/vendor/autoload.php');
        $executable = new File('/foo/bar/vendor/bin/captainhook');
        $template   = new Local($repo, $config, $bootstrap, $executable, false);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('$captainHook->run(', $code);
    }

    /**
     * Tests Local::getCode
     */
    public function testPharTemplate(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $bootstrap  = new File('/foo/bar/vendor/autoload.php');
        $executable = new File('/foo/bar/tools/captainhook.phar');
        $template   = new Local($repo, $config, $bootstrap, $executable, true);
        $code       = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('hook:commit-msg', $code);
        $this->assertStringContainsString('tools/captainhook.phar', $code);
    }
}
