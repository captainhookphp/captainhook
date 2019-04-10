<?php

namespace CaptainHook\App\Console\IO;

use PHPUnit\Framework\TestCase;

class NullIOTest extends TestCase
{
    /**
     * Tests NullIO::getArguments
     */
    public function testGetArguments()
    {
        $io = new NullIO();
        $this->assertEquals([], $io->getArguments());
    }

    /**
     * Tests NullIO::getArgument
     */
    public function testGetArgument()
    {
        $io = new NullIO();
        $this->assertEquals('', $io->getArgument('foo'));
        $this->assertEquals('bar', $io->getArgument('foo', 'bar'));
    }

    /**
     * Tests NullIO::isInteractive
     */
    public function testIsInteractive()
    {
        $io = new NullIO();
        $this->assertFalse($io->isInteractive());
    }

    /**
     * Tests NullIO::isDebug
     */
    public function testIsDebug()
    {
        $io = new NullIO();
        $this->assertFalse($io->isDebug());
    }

    /**
     * Tests NullIO::isVerbose
     */
    public function testIsVerbose()
    {
        $io = new NullIO();
        $this->assertFalse($io->isVerbose());
    }

    /**
     * Tests NullIO::isVeryVerbose
     */
    public function testIsVeryVerbose()
    {
        $io = new NullIO();
        $this->assertFalse($io->isVeryVerbose());
    }

    /**
     * Tests NullIO::writeError
     */
    public function testWriteError()
    {
        $io = new NullIO();
        $io->writeError('foo');
        $this->assertTrue(true);
    }

    /**
     * Tests NullIO::ask
     */
    public function testAsk()
    {
        $io = new NullIO();
        $this->assertEquals('bar', $io->ask('foo', 'bar'));
    }

    /**
     * Tests NullIO::askConfirmation
     */
    public function testAskConfirmation()
    {
        $io = new NullIO();
        $this->assertEquals(true, $io->askConfirmation('foo', true));
    }

    /**
     * Tests NullIO::askAbdValidate
     */
    public function testAskAndValidate()
    {
        $io = new NullIO();
        $this->assertEquals(
            true,
            $io->askAndValidate(
                'foo',
                function() {
                    return true;
                },
                false,
                true
            )
        );
    }
}
