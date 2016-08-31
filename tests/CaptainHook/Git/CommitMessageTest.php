<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Git;

class CommitMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CommitMessage::isEmpty
     */
    public function testIsEmpty()
    {
        $msg = new CommitMessage('');
        $this->assertTrue($msg->isEmpty());
    }

    public function testGetContent()
    {
        $content = 'Foo' . PHP_EOL . 'Bar' . PHP_EOL . 'Baz';
        $msg = new CommitMessage($content);
        $this->assertEquals($content, $msg->getContent());
    }

    /**
     * Tests CommitMessage::getLines
     */
    public function testGetLines()
    {
        $msg   = new CommitMessage('Foo' . PHP_EOL . 'Bar' . PHP_EOL . 'Baz');
        $lines = $msg->getLines();
        $this->assertTrue(is_array($lines));
        $this->assertEquals(3, count($lines));
    }

    /**
     * Tests CommitMessage::getLineCount
     */
    public function testLineCodeOnEmptyMessage()
    {
        $msg = new CommitMessage('');
        $this->assertEquals(0, $msg->getLineCount());
    }

    /**
     * Tests CommitMessage::getLineCount
     */
    public function testLineCount()
    {
        $msg = new CommitMessage('Foo' . PHP_EOL . 'Bar' . PHP_EOL . 'Baz');
        $this->assertEquals(3, $msg->getLineCount());
    }

    /**
     * Tests CommitMessage::getSubject
     */
    public function testGetSubject()
    {
        $msg = new CommitMessage('Foo' . PHP_EOL . 'Bar' . PHP_EOL . 'Baz');
        $this->assertEquals('Foo', $msg->getSubject());
    }

    /**
     * Tests CommitMessage::getBody
     */
    public function testGetBody()
    {
        $msg = new CommitMessage('Foo' . PHP_EOL . PHP_EOL . 'Bar' . PHP_EOL . 'Baz');
        $this->assertEquals('Bar' . PHP_EOL . 'Baz', $msg->getBody());
    }

    /**
     * Tests CommitMessage::getBodyLines
     */
    public function testGetBodyLines()
    {
        $msg   = new CommitMessage('Foo' . PHP_EOL . PHP_EOL . 'Bar' . PHP_EOL . 'Baz');
        $lines = $msg->getBodyLines();
        $this->assertEquals(2, count($lines));
        $this->assertEquals('Bar', $lines[0]);
        $this->assertEquals('Baz', $lines[1]);
    }

    /**
     * Tests CommitMessage::getLine
     */
    public function testGetLine()
    {
        $msg = new CommitMessage('Foo' . PHP_EOL . 'Bar' . PHP_EOL . 'Baz');
        $this->assertEquals('Foo', $msg->getLine(0));
        $this->assertEquals('Bar', $msg->getLine(1));
        $this->assertEquals('Baz', $msg->getLine(2));
    }

    /**
     * Tests CommitMessage::createFromFile
     *
     * @expectedException \Exception
     */
    public function testCreateFromFile()
    {
        CommitMessage::createFromFile('iDoNotExist.txt');
    }
}
