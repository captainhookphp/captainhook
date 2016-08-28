<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Runner;

use HookMeUp\Console\IOUtil;
use HookMeUp\Hook\Template;
use HookMeUp\Runner;
use HookMeUp\Exception;
use HookMeUp\Storage\File;
use HookMeUp\Hook\Util;

/**
 * Class Installer
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
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
     * @return \HookMeUp\Runner\Installer
     */
    public function setForce($force)
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
            $this->installHook($hook, $ask);
        }
    }

    /**
     * Return list of hooks to install.
     *
     * @return array
     */
    public function getHooksToInstall()
    {
        return null === $this->hookToHandle ? Util::getValidHooks() : [$this->hookToHandle => false];
    }

    /**
     * Install given hook.
     *
     * @param string $hook
     * @param bool   $ask
     */
    public function installHook($hook, $ask)
    {
        $doIt = true;
        if ($ask) {
            $answer = $this->io->ask('    <info>Install \'' . $hook . '\' hook [y,n]?</info> ', 'y');
            $doIt = IOUtil::answerToBool($answer);
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
    public function writeHookFile($hook)
    {
        $hooksDir = $this->repository->getHooksDir();
        $hookFile = $hooksDir . DIRECTORY_SEPARATOR . $hook;
        $doIt     = true;

        // if hook is configured and no force option is set
        // ask the user if overwriting the hook is ok
        if ($this->repository->hookExists($hook) && !$this->force) {
            $answer = $this->io->ask('    <comment>The \'' . $hook . '\' hook exists! Overwrite [y,n]?</comment> ', 'n');
            $doIt   = IOUtil::answerToBool($answer);
        }

        if ($doIt) {
            $code = Template::getCode($hook);
            $file = new File($hookFile);
            $file->write($code);
            chmod($hookFile, 0755);
            $this->io->write('    <info>\'' . $hook . '\' hook installed successfully</info>');
        }
    }
}
