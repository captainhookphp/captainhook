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

use CaptainHook\App\Storage\Util;
use PHPUnit\Framework\TestCase;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

class LocalTest extends TestCase
{
    /**
     * Tests Local::getCode
     */
    public function testTemplate() : void
    {
        $repo     = new Directory('/foo/bar');
        $vendor   = new Directory('/foo/bar/vendor');
        $config   = new File('/foo/bar/captainhook.json');
        $template = new Local($repo, $vendor, $config);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('$app->setHook(\'commit-msg\');', $code);
        $this->assertStringContainsString('$app->run();', $code);
        $this->assertStringContainsString('__DIR__ . \'/../../captain', $code);
        $this->assertStringContainsString('__DIR__ . \'/../../vendor', $code);
    }
}
