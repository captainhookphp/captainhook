<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook;

class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Util::isValid
     */
    public function testIsValid()
    {
        $this->assertTrue(Util::isValid('pre-commit'));
        $this->assertTrue(Util::isValid('pre-push'));
        $this->assertTrue(Util::isValid('commit-msg'));
        $this->assertFalse(Util::isValid('foo'));
    }

    /**
     * Tests Util::getValidHooks
     */
    public function testGetValidHooks()
    {
        $this->assertTrue(array_key_exists('pre-commit', Util::getValidHooks()));
        $this->assertTrue(array_key_exists('pre-push', Util::getValidHooks()));
        $this->assertTrue(array_key_exists('commit-msg', Util::getValidHooks()));
    }

    /**
     * Tests Util::getHooks
     */
    public function testGetHooks()
    {
        $this->assertTrue(in_array('pre-commit', Util::getHooks()));
        $this->assertTrue(in_array('pre-push', Util::getHooks()));
        $this->assertTrue(in_array('commit-msg', Util::getHooks()));
    }

    /**
     * Tests Util::getActionType
     */
    public function testGetActionType()
    {
        $this->assertEquals('php', Util::getActionType('\\Foo\\Bar'));
        $this->assertEquals('cli', Util::getActionType('echo foo'));
        $this->assertEquals('cli', Util::getActionType('/usr/local/bin/phpunit.phar'));
    }
}
