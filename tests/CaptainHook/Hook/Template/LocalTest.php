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

class LocalTest extends TestCase
{
    /**
     * Tests Local::getCode
     */
    public function testTemplate() : void
    {
        $repo     = Util::arrayToPath(['foo', 'bar'], true);
        $vendor   = Util::arrayToPath(['foo', 'bar', 'vendor'], true);
        $config   = Util::arrayToPath(['foo', 'bar', 'captainhook.json'], true);
        $template = new Local($repo, $vendor, $config);
        $code     = $template->getCode('commit-msg');

        $this->assertStringContainsString('#!/usr/bin/env php', $code);
        $this->assertStringContainsString('$app->setHook(\'commit-msg\');', $code);
        $this->assertStringContainsString('$app->run();', $code);
        $this->assertStringContainsString('__DIR__ . \'/../../captain', $code);
        $this->assertStringContainsString('__DIR__ . \'/../../vendor', $code);
    }
}
