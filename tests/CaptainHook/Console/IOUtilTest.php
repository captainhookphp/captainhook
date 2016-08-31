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

class IOUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests IOUtil::answerToBool
     */
    public function testAnswerToBool()
    {
        $this->assertTrue(IOUtil::answerToBool('y'));
        $this->assertTrue(IOUtil::answerToBool('yes'));
        $this->assertTrue(IOUtil::answerToBool('ok'));
        $this->assertFalse(IOUtil::answerToBool('foo'));
    }
}
