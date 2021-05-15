<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\Constrained;
use CaptainHook\App\Hook\Message\RuleBook;
use CaptainHook\App\Hook\Restriction;
use CaptainHook\App\Hooks;
use SebastianFeldmann\Cli\Output\Util as OutputUtil;
use SebastianFeldmann\Git\Repository;

/**
 * Class Book
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Book implements Action, Constrained
{
    /**
     * Returns a list of applicable hooks
     *
     * @return \CaptainHook\App\Hook\Restriction
     */
    public static function getRestriction(): Restriction
    {
        return Restriction::fromArray([Hooks::COMMIT_MSG]);
    }

    /**
     * Execute the configured action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    abstract public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void;

    /**
     * Validate the message
     *
     * @param  \CaptainHook\App\Hook\Message\RuleBook $ruleBook
     * @param  \SebastianFeldmann\Git\Repository      $repository
     * @param  \CaptainHook\App\Console\IO            $io
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function validate(RuleBook $ruleBook, Repository $repository, IO $io): void
    {
        // if this is a merge commit skip enforcing message rules
        if ($repository->isMerging()) {
            return;
        }

        $problems = $ruleBook->validate($repository->getCommitMsg());

        if (count($problems)) {
            throw new ActionFailed($this->getErrorOutput($problems, $repository));
        }
    }

    /**
     * Format the error output
     *
     * @param  array<string>                     $problems
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @return string
     */
    private function getErrorOutput(array $problems, Repository $repository): string
    {
        $err  = count($problems);
        $head = [
            IOUtil::getLineSeparator(80, '-'),
            '<error>CAPTAINHOOK FOUND ' . $err
            . ' PROBLEM' . ($err === 1 ? '' : 'S')
            . ' IN YOUR COMMIT MESSAGE</error>',
            IOUtil::getLineSeparator(80, '-')
        ];
        $msg   = OutputUtil::trimEmptyLines($repository->getCommitMsg()->getLines());
        $lines = [IOUtil::getLineSeparator(80, '-')];
        foreach ($problems as $problem) {
            $lines[] = '  ' . $this->formatProblem($problem);
        }
        $lines[] = IOUtil::getLineSeparator(80, '-');

        return implode(PHP_EOL, array_merge($head, $msg, $lines));
    }

    /**
     * Indent multi line problems so the lines after the first one are indented for better readability
     *
     * @param  string $problem
     * @return string
     */
    private function formatProblem(string $problem): string
    {
        $lines  = explode(PHP_EOL, $problem);
        $amount = count($lines);

        for ($i = 1; $i < $amount; $i++) {
            $lines[$i] = '    ' . $lines[$i];
        }

        return implode(PHP_EOL, $lines);
    }
}
