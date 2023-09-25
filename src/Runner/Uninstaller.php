<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Runner;

use CaptainHook\App\Console\IOUtil;

/**
 * Class Uninstaller
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.17.0
 */
class Uninstaller extends Files
{
    /**
     * Execute installation
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->getHooksToHandle() as $hook => $ask) {
            $this->uninstallHook($hook, ($ask && !$this->force));
        }
    }

    /**
     * Install given hook
     *
     * @param string $hook
     * @param bool   $ask
     */
    private function uninstallHook(string $hook, bool $ask): void
    {
        if (!$this->repository->hookExists($hook)) {
            return;
        }

        $doIt = true;
        if ($ask) {
            $answer = $this->io->ask('  <info>Remove \'' . $hook . '\' hook?</info> <comment>[y,n]</comment> ', 'y');
            $doIt   = IOUtil::answerToBool($answer);
        }

        if ($doIt) {
            if ($this->shouldHookBeMoved()) {
                $this->backupHook($hook);
                return;
            }
            unlink($this->repository->getHooksDir() . DIRECTORY_SEPARATOR . $hook);
        }
    }
}
