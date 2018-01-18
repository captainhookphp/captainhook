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

use SebastianFeldmann\CaptainHook\Hook\Message\Rule;

/**
 * Class RuleSet
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 2.1.0
 */
abstract class RuleSet
{
    /**
     * Return Beams rule set.
     *
     * @param  int $subjectLength
     * @param  int $bodyLineLength
     * @return \SebastianFeldmann\CaptainHook\Hook\Message\Rule[]
     */
    public static function beams(int $subjectLength = 50, int $bodyLineLength = 72) : array
    {
        return [
            new Rule\CapitalizeSubject(),
            new Rule\LimitSubjectLength($subjectLength),
            new Rule\NoPeriodOnSubjectEnd(),
            new Rule\UseImperativeMood(),
            new Rule\LimitBodyLineLength($bodyLineLength),
            new Rule\SeparateSubjectFromBodyWithBlankLine()
        ];
    }
}
