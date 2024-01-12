<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Git\Range;

use CaptainHook\App\Console\IO;
use CaptainHook\App\Hooks;
use SebastianFeldmann\Git\Repository;

/**
 * Detector class
 *
 * Responsible for finding the previous - current ranges in every scenario
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.15.0
 */
class Detector
{
    /**
     * List of available range detectors
     *
     * @var array<string, string>
     */
    private static array $detectors = [
        'hook:pre-push'      => '\\CaptainHook\\App\\Hook\\Input\\PrePush',
        'hook:post-rewrite'  => '\\CaptainHook\\App\\Hook\\Input\\PostRewrite',
    ];

    /**
     * Returns the list of ranges to check
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \SebastianFeldmann\Git\Repository $repository
     *
     * @return array<\CaptainHook\App\Git\Range>
     */
    public static function getRanges(IO $io, Repository $repository): array
    {
        $command = $io->getArgument(Hooks::ARG_COMMAND);

        /** @var \CaptainHook\App\Git\Range\Detecting $class */
        $class    = self::$detectors[$command] ?? '\\CaptainHook\\App\\Git\\Range\\Detector\\Fallback';
        $detector = new $class();

        return $detector->getRanges($io, $repository);
    }
}
