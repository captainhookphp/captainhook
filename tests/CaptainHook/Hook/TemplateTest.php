<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Template::getCode
     */
    public function testCode()
    {
        $code = Template::getCode('commit-msg', '/foo/bar', '/foo/bar/vendor', '/foo/bar/captainhook.json');

        $this->assertTrue(strpos($code, '#!/usr/bin/env php') !== false);
        $this->assertTrue(strpos($code, '$app->setHook(\'commit-msg\');') !== false);
        $this->assertTrue(strpos($code, '$app->run();') !== false);
        $this->assertTrue(strpos($code, '__DIR__ . \'/../../captain') !== false);
        $this->assertTrue(strpos($code, '__DIR__ . \'/../../vendor') !== false);
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
