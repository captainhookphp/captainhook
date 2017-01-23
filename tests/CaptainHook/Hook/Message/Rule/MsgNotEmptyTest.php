<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message\Rule;

use SebastianFeldmann\Git\CommitMessage;

class MsgNotEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests MsgNotEmpty::pass
     */
    public function testPassSuccess()
    {
        $msg  = new CommitMessage('Foo bar');
        $rule = new MsgNotEmpty();
        $this->assertTrue($rule->pass($msg));
    }

    /**
     * Tests MsgNotEmpty::pass
     */
    public function testPassFail()
    {
        $msg  = new CommitMessage('');
        $rule = new MsgNotEmpty();
        $this->assertFalse($rule->pass($msg));
    }
}
