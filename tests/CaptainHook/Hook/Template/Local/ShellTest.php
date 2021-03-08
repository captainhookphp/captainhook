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
        $phpPath    = '';

        $template = new Shell($repo, $config, $executable, $bootstrap, false, $phpPath);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringNotContainsString('php7.4', $code);
        $this->assertStringContainsString('vendor/bin/captainhook $INTERACTIVE', $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateWithDefinedPHP(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $executable = new File('/foo/bar/vendor/bin/captainhook');
        $bootstrap  = 'vendor/autoload.php';
        $phpPath    = '/usr/bin/php7.4';

        $template = new Shell($repo, $config, $executable, $bootstrap, false, $phpPath);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/bin/php7.4', $code);
        $this->assertStringContainsString('vendor/bin/captainhook $INTERACTIVE', $code);
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
        $phpPath    = '';

        $template = new Shell($repo, $config, $executable, $bootstrap, false, $phpPath);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringNotContainsString('php7.4', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringNotContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateExtExecutableWithDefinedPHP(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $executable = new File('/usr/local/bin/captainhook');
        $bootstrap  = 'vendor/autoload.php';
        $phpPath    = '/usr/bin/php7.4';

        $template = new Shell($repo, $config, $executable, $bootstrap, false, $phpPath);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);

        $this->assertStringContainsString('/usr/bin/php7.4', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringNotContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Tests Shell::getCode
     */
    public function testTemplateExtExecutableWithUserInput(): void
    {
        $repo       = new Directory('/foo/bar');
        $config     = new File('/foo/bar/captainhook.json');
        $executable = new File('/usr/local/bin/captainhook');
        $bootstrap  = 'vendor/autoload.php';
        $phpPath    = '';

        $template = new Shell($repo, $config, $executable, $bootstrap, false, $phpPath);
        $code     = $template->getCode('prepare-commit-msg');

        $this->assertStringContainsString('#!/bin/sh', $code);
        $this->assertStringContainsString('commit-msg', $code);
        $this->assertStringContainsString('/usr/local/bin/captainhook $INTERACTIVE', $code);
        $this->assertStringContainsString($this->getTtyRedirectionLines(), $code);
    }

    /**
     * Returns the expected TTY redirection lines
     *
     * @return string
     */
    private function getTtyRedirectionLines(): string
    {
        return <<<'EOD'
if [ -t 1 ]; then
    # If we're in a terminal, redirect stdout and stderr to /dev/tty and
    # read stdin from /dev/tty. Allow interactive mode for CaptainHook.
    exec >/dev/tty 2>/dev/tty </dev/tty
    INTERACTIVE=""
fi
EOD;
    }
}
