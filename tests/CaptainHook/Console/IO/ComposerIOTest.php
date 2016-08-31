<?php

namespace CaptainHook\Console\IO;

class ComposerIOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CaptainHook\Console\IO;
     */
    private $io;

    /**
     * Test setup
     */
    public function setUp()
    {
        $mock = $this->getMockBuilder('\\Composer\\IO\\IOInterface')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('isInteractive')->willReturn(false);
        $mock->method('isDebug')->willReturn(false);
        $mock->method('isVerbose')->willReturn(false);
        $mock->method('isVeryVerbose')->willReturn(false);
        $mock->method('ask')->willReturn('bar');
        $mock->method('askConfirmation')->willReturn(true);
        $mock->method('askAndValidate')->willReturn(true);
        $this->io = new ComposerIO($mock);
    }

    /**
     * Test tear down
     */
    public function tearDown()
    {
        $this->io = null;
    }

    /**
     * Tests ComposerIO::isInteractive
     */
    public function testIsInteractive()
    {
        $this->assertFalse($this->io->isInteractive());
    }

    /**
     * Tests ComposerIO::isDebug
     */
    public function testIsDebug()
    {
        $this->assertFalse($this->io->isDebug());

    }

    /**
     * Tests ComposerIO::isVerbose
     */
    public function testIsVerbose()
    {
        $this->assertFalse($this->io->isVerbose());
    }

    /**
     * Tests ComposerIO::isVeryVerbose
     */
    public function testIsVeryVerbose()
    {
        $this->assertFalse($this->io->isVeryVerbose());
    }

    /**
     * Tests ComposerIO::write
     */
    public function testWrite()
    {
        $this->io->write('foo');
    }

    /**
     * Tests ComposerIO::writeError
     */
    public function testWriteError()
    {
        $this->io->writeError('foo');
    }

    /**
     * Tests ComposerIO::ask
     */
    public function testAsk()
    {
        $this->assertEquals('bar', $this->io->ask('foo', 'bar'));
    }

    /**
     * Tests ComposerIO::askConfirmation
     */
    public function testAskConfirmation()
    {
        $this->assertEquals(true, $this->io->askConfirmation('foo', true));
    }

    /**
     * Tests ComposerIO::askAbdValidate
     */
    public function testAskAndValidate()
    {
        $this->assertEquals(true, $this->io->askAndValidate('foo', function() { return true; }, false, true));
    }
}
