<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message\RuleBook;

use SebastianFeldmann\Git\CommitMessage;

class RuleSetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests RuleSet::beams
     */
    public function testRuleSetBeams()
    {
        $msg   = new CommitMessage('Foo bar baz' . PHP_EOL . PHP_EOL . 'This is a longer body line.');
        $rules = RuleSet::beams();

        $this->assertEquals(6, count($rules));

        foreach ($rules as $rule) {
            $this->assertTrue($rule->pass($msg));
        }
    }
}
