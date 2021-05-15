<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use SebastianFeldmann\Git\Repository;

/**
 * Debug hook to test hook triggering
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.0.4
 */
class Debug implements Action
{
    /**
     * Executes the action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $originalHookArguments = $io->getArguments();
        $currentGitTag         = $repository->getInfoOperator()->getCurrentTag();

        $io->write(['', '']);
        $io->write('<info>Executing Dummy action</info>');
        $io->write($this->getArgumentOutput($originalHookArguments));
        $io->write('  Current git-tag: <comment>' . $currentGitTag . '</comment>');
        $io->write('StandardInput:' . PHP_EOL . implode(PHP_EOL, $io->getStandardInput()));

        throw new ActionFailed(
            'The \'Debug\' action is only for debugging purposes, '
            . 'please remove the \'Debug\' action from your config'
        );
    }

    /**
     * Format output to display original hook arguments
     *
     * @param  array<string> $args
     * @return string
     */
    protected function getArgumentOutput(array $args): string
    {
        $out = '  Original arguments:' . PHP_EOL;
        foreach ($args as $name => $value) {
            $out .= '    ' . $name . ' => <comment>' . $value . '</comment>' . PHP_EOL;
        }
        return $out;
    }
}
