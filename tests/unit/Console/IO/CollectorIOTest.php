<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\IO;

use PHPUnit\Framework\TestCase;

class CollectorIOTest extends TestCase
{
    use Mockery;

    /**
     * Tests CollectorIO::getArguments
     */
    public function testGetArguments(): void
    {
        $io  = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertEquals([], $cio->getArguments());
    }

    /**
     * Tests CollectorIO::getArgument
     */
    public function testGetArgument(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertEquals('', $cio->getArgument('foo'));
        $this->assertEquals('bar', $cio->getArgument('foo', 'bar'));
    }

    /**
     * Tests CollectorIO::getStandardInput
     */
    public function testGetStandardInput(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertEquals([], $cio->getStandardInput());
    }

    /**
     * Tests CollectorIO::isInteractive
     */
    public function testIsInteractive(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertFalse($cio->isInteractive());
    }

    /**
     * Tests CollectorIO::isDebug
     */
    public function testIsDebug(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertFalse($cio->isDebug());
    }

    /**
     * Tests CollectorIO::isVerbose
     */
    public function testIsVerbose(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertFalse($cio->isVerbose());
    }

    /**
     * Tests CollectorIO::isVeryVerbose
     */
    public function testIsVeryVerbose(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertFalse($cio->isVeryVerbose());
    }

    /**
     * Tests CollectorIO::writeError
     */
    public function testWriteError(): void
    {
        $io = new NullIO();
        $cio = new CollectorIO($io);
        $cio->writeError('foo');
        $this->assertTrue(true);
    }

    /**
     * Tests CollectorIO::ask
     */
    public function testAsk(): void
    {
        $io  = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertEquals('bar', $cio->ask('foo', 'bar'));
    }

    /**
     * Tests CollectorIO::askConfirmation
     */
    public function testAskConfirmation(): void
    {
        $io  = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertEquals(true, $cio->askConfirmation('foo', true));
    }

    /**
     * Tests CollectorIO::askAbdValidate
     *
     * @throws \Exception
     */
    public function testAskAndValidate(): void
    {
        $io  = new NullIO();
        $cio = new CollectorIO($io);
        $this->assertTrue(
            $cio->askAndValidate(
                'foo',
                function () {
                    return true;
                },
                false,
                true
            )
        );
    }
}
