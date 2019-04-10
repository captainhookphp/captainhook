<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

use PHPUnit\Framework\TestCase;

class UtilTest extends TestCase
{
    /**
     * Tests Util::getExecType
     */
    public function testGetTypePHP()
    {
        $this->assertEquals('php', Util::getExecType('\\Foo\\Bar'));
        $this->assertEquals('php', Util::getExecType('\\Foo\\Bar::baz'));
        $this->assertEquals('php', Util::getExecType('\\Fiz'));
    }

    /**
     * Tests Util::getExecType
     */
    public function testGetTypeCli()
    {
        $this->assertEquals('cli', Util::getExecType('./my-binary'));
        $this->assertEquals('cli', Util::getExecType('phpunit'));
        $this->assertEquals('cli', Util::getExecType('~/composer install'));
        $this->assertEquals('cli', Util::getExecType('echo foo'));
        $this->assertEquals('cli', Util::getExecType('/usr/local/bin/phpunit.phar'));
    }

    /**
     * Tests Util::isTypeValid
     */
    public function testIsTypeValid()
    {
        $this->assertTrue(Util::isTypeValid('php'));
        $this->assertTrue(Util::isTypeValid('cli'));
        $this->assertFalse(Util::isTypeValid('foo'));
    }
}
