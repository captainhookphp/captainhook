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

class UseImperativeMoodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests UseImperativeMood::pass
     */
    public function testPassSuccess()
    {
        $msg  = new CommitMessage('Foo bar baz');
        $rule = new UseImperativeMood();
        $this->assertTrue($rule->pass($msg));
    }

    /**
     * Tests UseImperativeMood::pass
     */
    public function testPassFail()
    {
        $msg  = new CommitMessage('Added some something');
        $rule = new UseImperativeMood();
        $this->assertFalse($rule->pass($msg));

        $hint = $rule->getHint();
        $this->assertTrue((bool) strpos($hint, 'imperative'));
    }
}
