<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class IOUtilTest extends TestCase
{
    /**
     * Tests IOUtil::mapConfigVerbosity
     */
    public function testMapConfigVerbosity(): void
    {
        $this->assertEquals(OutputInterface::VERBOSITY_QUIET, IOUtil::mapConfigVerbosity('quiet'));
        $this->assertEquals(OutputInterface::VERBOSITY_NORMAL, IOUtil::mapConfigVerbosity('normal'));
        $this->assertEquals(OutputInterface::VERBOSITY_VERBOSE, IOUtil::mapConfigVerbosity('verbose'));
        $this->assertEquals(OutputInterface::VERBOSITY_DEBUG, IOUtil::mapConfigVerbosity('debug'));
    }

    /**
     * Tests IOUtil::mapConfigVerbosity
     */
    public function testMapConfigVerbosityNotFound(): void
    {
        $this->assertEquals(OutputInterface::VERBOSITY_NORMAL, IOUtil::mapConfigVerbosity('foobar'));
    }

    /**
     * Tests IOUtil::answerToBool
     */
    public function testAnswerToBool(): void
    {
        $this->assertTrue(IOUtil::answerToBool('y'));
        $this->assertTrue(IOUtil::answerToBool('yes'));
        $this->assertTrue(IOUtil::answerToBool('ok'));
        $this->assertFalse(IOUtil::answerToBool('foo'));
    }
}
