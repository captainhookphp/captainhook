<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Validator\Rule;

use CaptainHook\App\Git\CommitMessage;

class SeparateSubjectFromBodyWithBlankLineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests SeparateSubjectFromBodyWithBlankLine::pass
     */
    public function testPassSuccessOnSubjectOnly()
    {
        $msg  = new CommitMessage('Foo bar');
        $rule = new SeparateSubjectFromBodyWithBlankLine();
        $this->assertTrue($rule->pass($msg));
    }

    /**
     * Tests SeparateSubjectFromBodyWithBlankLine::pass
     */
    public function testPassSuccessWithBody()
    {
        $msg  = new CommitMessage('Foo bar' . PHP_EOL . PHP_EOL . 'Foo Bar Baz.');
        $rule = new SeparateSubjectFromBodyWithBlankLine();
        $this->assertTrue($rule->pass($msg));
    }

    /**
     * Tests SeparateSubjectFromBodyWithBlankLine::pass
     */
    public function testPassFailNoEmptyLine()
    {
        $msg  = new CommitMessage('Foo bar' . PHP_EOL . 'Foo Bar Baz.');
        $rule = new SeparateSubjectFromBodyWithBlankLine();
        $this->assertFalse($rule->pass($msg));
    }
}
