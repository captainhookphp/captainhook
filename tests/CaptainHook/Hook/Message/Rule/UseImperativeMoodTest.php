<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Rule;

use SebastianFeldmann\Git\CommitMessage;
use PHPUnit\Framework\TestCase;

class UseImperativeMoodTest extends TestCase
{
    /**
     * Tests UseImperativeMood::pass
     *
     * @dataProvider passProvider
     */
    public function testPass(string $string, bool $begin, bool $expectedResult)
    {
        $msg  = new CommitMessage($string);
        $rule = new UseImperativeMood($begin);

        $this->assertSame($expectedResult, $rule->pass($msg));
    }

    public function passProvider() : array
    {
        return [
            ['foo bar baz', true, true],
            ['foo bar baz', false, true],
            ['fixed soemthing something', false, false],
            ['fixed soemthing something', true, false],
            ['soemthing fixed something', false, false],
            ['soemthing fixed something', true, true],
        ];
    }
}
