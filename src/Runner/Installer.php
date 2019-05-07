<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner;

use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Exception;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hook\Util;
use CaptainHook\App\Storage\File;

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
     * Overwrite hook
     *
     * @var bool
     */
    private $force;

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
     * @param  bool $force
     * @return \CaptainHook\App\Runner\Installer
     */
    public function setForce(bool $force) : Installer
    {
        $this->force = $force;
        return $this;
    }

    /**
     * Hook setter
     *
     * @param  string $hook
     * @return \CaptainHook\App\Runner\Installer
     * @throws \CaptainHook\App\Exception\InvalidHookName
     */
    public function setHook(string $hook) : Installer
    {
        if (!empty($hook) && !Util::isValid($hook)) {
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
    public function run() : void
    {
        $hooks = $this->getHooksToInstall();

        foreach ($hooks as $hook => $ask) {
            $this->installHook($hook, ($ask && !$this->force));
        }
    }

    /**
     * Return list of hooks to install
     *
     * @return array
     */
    public function getHooksToInstall() : array
    {
        return empty($this->hookToHandle) ? Util::getValidHooks() : [$this->hookToHandle => false];
    }

    /**
     * Install given hook
     *
     * @param string $hook
     * @param bool   $ask
     */
    public function installHook(string $hook, bool $ask): void
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
     * @param  string $hook
     * @return void
     */
    public function writeHookFile(string $hook) : void
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
    protected function getHookSourceCode(string $hook) : string
    {
        return $this->template->getCode($hook);
    }

    /**
     * If the hook already exists the user has to confirm the installation
     *
     * @param  string $hook The name of the hook to check
     * @return bool
     */
    protected function needInstallConfirmation(string $hook) : bool
    {
        return $this->repository->hookExists($hook) && !$this->force;
    }

    /**
     * Set used hook template
     *
     * @param Template $template
     *
     * @return Installer
     */
    public function setTemplate(Template $template) : Installer
    {
        $this->template = $template;
        return $this;
    }
}
