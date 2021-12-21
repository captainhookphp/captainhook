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

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Exception;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hook\Util as HookUtil;
use CaptainHook\App\Hooks;
use CaptainHook\App\Storage\File;
use RuntimeException;
use SebastianFeldmann\Camino\Check;
use SebastianFeldmann\Git\Repository;

/**
 * Class Installer
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Installer extends RepositoryAware
{
    /**
     * Install hooks brute force
     *
     * @var bool
     */
    private $force = false;

    /**
     * Don't overwrite existing hooks
     *
     * @var bool
     */
    private $skipExisting = false;

    /**
     * Path where the existing hooks should be moved to
     *
     * @var string
     */
    private $moveExistingTo = '';

    /**
     * Hook that should be handled.
     *
     * @var string
     */
    protected $hookToHandle;

    /**
     * Hook template
     *
     * @var Template
     */
    private $template;

    /**
     * Git repository.
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    protected $repository;

    /**
     * HookHandler constructor.
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \CaptainHook\App\Config           $config
     * @param \SebastianFeldmann\Git\Repository $repository
     * @param \CaptainHook\App\Hook\Template    $template
     */
    public function __construct(IO $io, Config $config, Repository $repository, Template $template)
    {
        $this->template = $template;
        parent::__construct($io, $config, $repository);
    }

    /**
     * @param  bool $force
     * @return \CaptainHook\App\Runner\Installer
     */
    public function setForce(bool $force): Installer
    {
        $this->force = $force;
        return $this;
    }

    /**
     * @param  bool $skip
     * @return \CaptainHook\App\Runner\Installer
     */
    public function setSkipExisting(bool $skip): Installer
    {
        if ($skip && !empty($this->moveExistingTo)) {
            throw new RuntimeException('choose --move-existing-to or --skip-existing');
        }
        $this->skipExisting = $skip;
        return $this;
    }

    /**
     * Set the path where the current hooks should be moved to
     *
     * @param  string $backup
     * @return \CaptainHook\App\Runner\Installer
     */
    public function setMoveExistingTo(string $backup): Installer
    {
        if (!empty($backup) && $this->skipExisting) {
            throw new RuntimeException('choose --skip-existing or --move-existing-to');
        }
        $this->moveExistingTo = $backup;
        return $this;
    }

    /**
     * Hook setter
     *
     * @param  string $hook
     * @return \CaptainHook\App\Runner\Installer
     * @throws \CaptainHook\App\Exception\InvalidHookName
     */
    public function setHook(string $hook): Installer
    {
        if (!empty($hook) && !HookUtil::isValid($hook)) {
            throw new Exception\InvalidHookName('Invalid hook name \'' . $hook . '\'');
        }
        $this->hookToHandle = $hook;
        return $this;
    }

    /**
     * Execute installation
     *
     * @return void
     */
    public function run(): void
    {
        $hooks = $this->getHooksToInstall();

        foreach ($hooks as $hook => $ask) {
            $this->installHook($hook, ($ask && !$this->force));
        }
    }

    /**
     * Return list of hooks to install
     *
     * [
     *   string    => bool
     *   HOOK_NAME => ASK_USER_TO_CONFIRM_INSTALL
     * ]
     *
     * @return array<string, bool>
     */
    public function getHooksToInstall(): array
    {
        // callback to write bool true to all array entries
        // to make sure the user will be asked to confirm every hook installation
        // unless the user provided the force or skip option
        $callback = function () {
            return true;
        };
        // if a specific hook is set, the use has actively chosen it, so don't ask for permission anymore
        return empty($this->hookToHandle)
            ? array_map($callback, Hooks::nativeHooks())
            : [$this->hookToHandle => false];
    }

    /**
     * Install given hook
     *
     * @param string $hook
     * @param bool   $ask
     */
    private function installHook(string $hook, bool $ask): void
    {
        if ($this->shouldHookBeSkipped($hook)) {
            $hint = $this->io->isDebug() ? ', remove the --skip-existing option to overwrite.' : '';
            $this->io->write('  <comment>' . $hook . '</comment> is already installed' . $hint, true, IO::VERBOSE);
            return;
        }

        $doIt = true;
        if ($ask) {
            $answer = $this->io->ask('  <info>Install \'' . $hook . '\' hook?</info> <comment>[Y,n]</comment> ', 'y');
            $doIt   = IOUtil::answerToBool($answer);
        }

        if ($doIt) {
            if ($this->shouldHookBeMoved()) {
                $this->backupHook($hook);
            }
            $this->writeHookFile($hook);
        }
    }

    /**
     * Check if the hook is installed and should be skipped
     *
     * @param  string $hook
     * @return bool
     */
    private function shouldHookBeSkipped(string $hook): bool
    {
        return $this->skipExisting && $this->repository->hookExists($hook);
    }

    /**
     * If a path to incorporate the existing hook is set we should incorporate existing hooks
     *
     * @return bool
     */
    private function shouldHookBeMoved(): bool
    {
        return !empty($this->moveExistingTo);
    }

    /**
     * Move the existing hook to the configured location
     *
     * @param string $hook
     */
    private function backupHook(string $hook): void
    {
        // no hook to move just leave
        if (!$this->repository->hookExists($hook)) {
            return;
        }

        $hookFileOrig   = $this->repository->getHooksDir() . DIRECTORY_SEPARATOR . $hook;
        $hookCmd        = rtrim($this->moveExistingTo, '/\\') . DIRECTORY_SEPARATOR . $hook;
        $hookCmdArgs    = $hookCmd . Hooks::getOriginalHookArguments($hook);
        $hookFileTarget = !Check::isAbsolutePath($this->moveExistingTo)
                        ? dirname($this->config->getPath()) . DIRECTORY_SEPARATOR . $hookCmd
                        : $hookCmd;

        $this->moveExistingHook($hookFileOrig, $hookFileTarget);

        $this->io->write(
            [
                '  Moved existing ' . $hook . ' hook to ' . $hookCmd,
                '  Add <comment>\'' . $hookCmdArgs . '\'</comment> to your '
                . $hook . ' configuration to execute it.'
            ]
        );
    }

    /**
     * Write given hook to .git/hooks directory
     *
     * @param  string $hook
     * @return void
     */
    private function writeHookFile(string $hook): void
    {
        $hooksDir = $this->repository->getHooksDir();
        $hookFile = $hooksDir . DIRECTORY_SEPARATOR . $hook;
        $doIt     = true;

        // if hook is configured and no force option is set
        // ask the user if overwriting the hook is ok
        if ($this->needInstallConfirmation($hook)) {
            $ans  = $this->io->ask('  <comment>The \'' . $hook . '\' hook exists! Overwrite? [y,n]</comment> ', 'n');
            $doIt = IOUtil::answerToBool($ans);
        }

        if ($doIt) {
            $code = $this->getHookSourceCode($hook);
            $file = new File($hookFile);
            $file->write($code);
            chmod($hookFile, 0755);
            $this->io->write('  <info>\'' . $hook . '\' hook installed successfully</info>');
        }
    }

    /**
     * Return the source code for a given hook script
     *
     * @param  string $hook
     * @return string
     */
    private function getHookSourceCode(string $hook): string
    {
        return $this->template->getCode($hook);
    }

    /**
     * If the hook already exists the user has to confirm the installation
     *
     * @param  string $hook The name of the hook to check
     * @return bool
     */
    private function needInstallConfirmation(string $hook): bool
    {
        return $this->repository->hookExists($hook) && !$this->force;
    }

    /**
     * Move the existing hook script to the new location
     *
     * @param  string $originalLocation
     * @param  string $newLocation
     * @return void
     * @throws \RuntimeException
     */
    private function moveExistingHook(string $originalLocation, string $newLocation): void
    {
        $dir = dirname($newLocation);
        // make sure the target directory isn't a file
        if (file_exists($dir) && !is_dir($dir)) {
            throw new RuntimeException($dir . ' is not a directory');
        }
        // create the directory if it does not exist
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // move the hook into the target directory
        rename($originalLocation, $newLocation);
    }
}
