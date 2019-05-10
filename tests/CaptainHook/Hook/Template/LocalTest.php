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

use CaptainHook\App\Hook\Template\Local;
use PHPUnit\Framework\TestCase;

class LocalTest extends TestCase
{
    /**
     * Tests Local::getCode
     */
    public function testTemplate() : void
    {
        $template = new Local('/foo/bar', '/foo/bar/vendor', '/foo/bar/captainhook.json');
        $code = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('$app->setHook(\'commit-msg\');', $code);
        $this->assertStringContainsString('$app->run();', $code);
        $this->assertStringContainsString('__DIR__ . \'/../../captain', $code);
        $this->assertStringContainsString('__DIR__ . \'/../../vendor', $code);
    }
}
