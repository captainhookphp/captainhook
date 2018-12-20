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

use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    /**
     * Tests Template::getCode
     */
    public function testCode()
    {
        $code = Template::getCode('commit-msg', '/foo/bar', '/foo/bar/vendor', '/foo/bar/captainhook.json');

        $this->assertContains('#!/usr/bin/env php', $code);
        $this->assertContains('$app->setHook(\'commit-msg\');', $code);
        $this->assertContains('$app->run();', $code);
        $this->assertContains('__DIR__ . \'/../../captain', $code);
        $this->assertContains('__DIR__ . \'/../../vendor', $code);
    }

    /**
     * Tests Template::getHookTargetPath
     */
    public function testGetTplTargetPath()
    {
        $path = Template::getTplTargetPath('/foo/bar', '/foo/bar/baz/vendor');

        $this->assertEquals('__DIR__ . \'/../../baz/vendor', $path);

        $path = Template::getTplTargetPath('/foo/bar', '/foo/bar/vendor');

        $this->assertEquals('__DIR__ . \'/../../vendor', $path);

        $path = Template::getTplTargetPath('/foo/bar', '/foo/bar/captainhook.json');

        $this->assertEquals('__DIR__ . \'/../../captainhook.json', $path);

        $path = Template::getTplTargetPath('/foo/bar', '/fiz/baz/captainhook.json');

        $this->assertEquals('\'/fiz/baz/captainhook.json', $path);
    }
}
