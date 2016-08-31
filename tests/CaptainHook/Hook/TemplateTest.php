<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Hook;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Template::getCode
     */
    public function testCode()
    {
        $code = Template::getCode('commit-msg');

        $this->assertTrue(strpos($code, '#!/usr/bin/env php') !== false);
        $this->assertTrue(strpos($code, '$app->setHook(\'commit-msg\');') !== false);
        $this->assertTrue(strpos($code, '$app->run();') !== false);
    }
}
