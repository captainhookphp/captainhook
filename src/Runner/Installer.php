<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner;

use SebastianFeldmann\CaptainHook\Console\IOUtil;
use SebastianFeldmann\CaptainHook\Hook\Template;
use SebastianFeldmann\CaptainHook\Storage\File;
use SebastianFeldmann\CaptainHook\Hook\Util;

/**
 * Class Installer
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 * @internal
 */
class Installer extends HookHandler
{
    /**
     * Overwrite hook
     *
     * @var bool
     */
    private $force;

    /**
     * @param  bool $force
     * @return \SebastianFeldmann\CaptainHook\Runner\Installer
     */
    public function setForce(bool $force)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * Execute installation.
     */
    public function run()
    {
        $hooks = $this->getHooksToInstall();

        foreach ($hooks as $hook => $ask) {
            $this->installHook($hook, ($ask && !$this->force));
        }
    }

    /**
     * Return list of hooks to install.
     *
     * @return array
     */
    public function getHooksToInstall() : array
    {
        return empty($this->hookToHandle) ? Util::getValidHooks() : [$this->hookToHandle => false];
    }

    /**
     * Install given hook.
     *
     * @param string $hook
     * @param bool   $ask
     */
    public function installHook(string $hook, bool $ask)
    {
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
     * Write given hook to .git/hooks directory
     *
     * @param string $hook
     */
    public function writeHookFile(string $hook)
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
     * Return the source code for a given hook script.
     *
     * @param  string $hook
     * @return string
     */
    protected function getHookSourceCode(string $hook) : string
    {
        $absRepoPath = realpath($this->repository->getRoot());
        $vendorPath  = getcwd() . '/vendor';
        $configPath  = realpath($this->config->getPath());
        return Template::getCode($hook, $absRepoPath, $vendorPath, $configPath);
    }

    /**
     * If the hook already exists the user has to confirm the installation.
     *
     * @param  string $hook The name of the hook to check
     * @return bool
     */
    protected function needInstallConfirmation(string $hook) : bool
    {
        return $this->repository->hookExists($hook) && !$this->force;
    }
}
