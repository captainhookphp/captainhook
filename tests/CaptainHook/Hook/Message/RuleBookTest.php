<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message;

use SebastianFeldmann\CaptainHook\Hook\Message\Rule\CapitalizeSubject;
use SebastianFeldmann\CaptainHook\Hook\Message\Rule\MsgNotEmpty;
use SebastianFeldmann\CaptainHook\Hook\Message\Rule\UseImperativeMood;
use SebastianFeldmann\Git\CommitMessage;

class RuleBookTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests RuleBook::validate
     */
    public function testValidOnEmptyRuleList()
    {
        $msg = new CommitMessage('Foo');
        $v   = new RuleBook();

        $v->validate($msg);
        $this->assertTrue(true);

    }

    /**
     * Tests RuleBook::setRules
     */
    public function testSetRulesValid()
    {
        $msg = new CommitMessage('Foo');
        $v   = new RuleBook();
        $v->setRules([new MsgNotEmpty()]);

        $v->validate($msg);
        $this->assertTrue(true);
    }

    /**
     * Tests RuleBook::setRules
     *
     * @expectedException \Exception
     */
    public function testSetRulesInvalid()
    {
        $msg = new CommitMessage('');
        $v   = new RuleBook();
        $v->setRules([new MsgNotEmpty()]);
        $v->validate($msg);
    }

    /**
     * Tests RuleBook::setRules
     *
     * @expectedException \Exception
     */
    public function testAddRuleInvalid()
    {
        $msg = new CommitMessage('foo bar baz');
        $v   = new RuleBook();
        $v->setRules([new MsgNotEmpty()]);
        $v->addRule(new CapitalizeSubject());

        $v->validate($msg);
    }

    /**
     * Tests RuleBook::setRules
     *
     * @expectedException \Exception
     */
    public function testAddRuleInvalidMultiLineProblem()
    {
        $msg = new CommitMessage('fixed bar baz');
        $v   = new RuleBook();
        $v->setRules([new UseImperativeMood()]);

        $v->validate($msg);
    }
}
