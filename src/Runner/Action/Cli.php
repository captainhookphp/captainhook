<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;

/**
 * Class Cli
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Cli
{
    /**
     * Execute the configured action
     *
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \CaptainHook\App\Config\Action  $action
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function execute(IO $io, Config\Action $action) : void
    {
        $processor = new Processor();
        $result    = $processor->run($this->formatCommand($action->getAction(), $io->getArguments()));

        if (!$result->isSuccessful()) {
            throw new Exception\ActionFailed($result->getStdOut() . PHP_EOL . $result->getStdErr());
        }
        $io->write(empty($result->getStdOut()) ? '<info>OK</info>' : $result->getStdOut());
    }

    /**
     * Replace argument placeholder with their original values
     *
     * This replaces the hook argument placeholder.
     *  - prepare-commit-msg => FILE, MODE, HASH
     *  - commit-msg         => FILE
     *  - pre-push           => TARGET, URL
     *  - pre-commit         => -
     *  - post-checkout      => PREVIOUSHEAD, NEWHEAD, MODE
     *  - post-merge         => SQUASH
     *
     * @param  string $command
     * @param  array  $args
     * @return string
     */
    protected function formatCommand(string $command, array $args) : string
    {
        foreach ($args as $key => $value) {
            $command = str_replace('{' . strtoupper($key) . '}', $value, $command);
        }
        return $command;
    }
}
