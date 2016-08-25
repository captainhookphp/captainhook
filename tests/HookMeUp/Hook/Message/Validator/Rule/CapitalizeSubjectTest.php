<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook\Message\Validator\Rule;

use HookMeUp\Git\CommitMessage;

class CapitalizeSubjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CapitalizeSubject::pass
     */
    public function testPassSuccess()
    {
        $msg  = new CommitMessage('Foo');
        $rule = new CapitalizeSubject();
        $this->assertTrue($rule->pass($msg));
    }

    /**
     * Tests CapitalizeSubject::pass
     */
    public function testPassFail()
    {
        $msg  = new CommitMessage('foo');
        $rule = new CapitalizeSubject();
        $this->assertFalse($rule->pass($msg));
    }

    /**
     * Tests CapitalizeSubject::pass
     */
    public function testPassFailOnEmptyMessage()
    {
        $msg  = new CommitMessage('');
        $rule = new CapitalizeSubject();
        $this->assertFalse($rule->pass($msg));
    }
}
