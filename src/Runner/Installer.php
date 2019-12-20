<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
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
use CaptainHook\App\Storage\File;
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
        $this->skipExisting = $skip;
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
        // unless the user provided the force option
        $callback = function () {
            return true;
        };
        // if a specific hook is set the user chose it so don't ask for permission anymore
        return empty($this->hookToHandle)
            ? array_map($callback, HookUtil::getValidHooks())
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
            $this->io->write($hook . ' is already installed, remove the --skip-existing option to overwrite.');
            return;
        }

        $doIt = true;
        if ($ask) {
            $answer = $this->io->ask('  <info>Install \'' . $hook . '\' hook?</info> <comment>[y,n]</comment> ', 'y');
            $doIt   = IOUtil::answerToBool($answer);
        }

        if ($doIt) {
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
}
