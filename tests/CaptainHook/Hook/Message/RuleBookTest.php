<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message;

use CaptainHook\App\Hook\Message\Rule\CapitalizeSubject;
use CaptainHook\App\Hook\Message\Rule\MsgNotEmpty;
use CaptainHook\App\Hook\Message\Rule\UseImperativeMood;
use SebastianFeldmann\Git\CommitMessage;
use PHPUnit\Framework\TestCase;

class RuleBookTest extends TestCase
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
