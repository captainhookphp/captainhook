<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Hook\Message\Validator\Rule;

use CaptainHook\Git\CommitMessage;

class LimitSubjectLengthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests LimitSubjectLength::pass
     */
    public function testPassSuccess()
    {
        $msg  = new CommitMessage('Foo Bar');
        $rule = new LimitSubjectLength(10);
        $this->assertTrue($rule->pass($msg));
    }

    /**
     * Tests LimitSubjectLength::pass
     */
    public function testPassFail()
    {
        $msg  = new CommitMessage('Foo Bar Baz Fiz Baz');
        $rule = new LimitSubjectLength(10);
        $this->assertFalse($rule->pass($msg));
    }
}
