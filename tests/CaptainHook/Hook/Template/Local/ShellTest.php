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

class ShellTest extends TestCase
{
    /**
     * Tests Shell::getCode
     */
    public function testTemplate(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $executable = new File('/foo/bar/vendor/bin/captainhook');
        $bootstrap  = 'vendor/autoload.php';

        $template = new Shell($repo, $config, $executable, $bootstrap, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('vendor/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateExtExecutable(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $executable = new File('/usr/local/bin/captainhook');
        $bootstrap  = 'vendor/autoload.php';

        $template = new Shell($repo, $config, $executable, $bootstrap, false);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringContainsString($this->getTtyRedirectionLines(), $code);
    }

    private function getTtyRedirectionLines(): string
    {
        return <<<'EOD'
INTERACTIVE="--no-interaction"
if [ -t 1 ]; then
    # If we're in a terminal, redirect stdout and stderr to /dev/tty and
    # read stdin from /dev/tty. Allow interactive mode for CaptainHook.
    exec >/dev/tty 2>/dev/tty </dev/tty
    INTERACTIVE=""
fi
EOD;
    }
}
