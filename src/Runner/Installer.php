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
class Installer extends Runner
{
    /**
     * Overwrite hook
     *
     * @var bool
     */
    private $force;

    /**
     * Hook that should be installed
     *
     * @var string
     */
    private $hookToInstall;

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
     * Define a hook you want to install.
     *
     * @param  string $hook
     * @return \HookMeUp\Runner\Installer
     * @throws \HookMeUp\Exception\InvalidHookName
     */
    public function setHook($hook)
    {
        if (null !== $hook && !Util::isValid($hook)) {
            throw new Exception\InvalidHookName('Invalid hook name \'' . $hook . '\'');
        }
        $this->hookToInstall = $hook;
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
        return null === $this->hookToInstall ? Util::getValidHooks() : [$this->hookToInstall => false];
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
            $doIt = ('y' === $answer);
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
        if ($this->repository->hookExists($hook) && !$this->force) {
            $answer = $this->io->ask('    <comment>The \'' . $hook . '\' hook exists! Overwrite [y,n]?</comment> ', 'n');
            $doIt   = ('y' === $answer);
        }

        if ($doIt) {
            $code = '#!/usr/bin/env php' . PHP_EOL .
            '<?php' . PHP_EOL .
            '$autoLoader = __DIR__ . \'/../../vendor/autoload.php\';' . PHP_EOL . PHP_EOL .
            'if (!file_exists($autoLoader)) {' . PHP_EOL .
            '    fwrite(STDERR,' . PHP_EOL .
            '        \'Composer autoload.php could not be found\' . PHP_EOL .' . PHP_EOL .
            '        \'Please re-install the hook with:\' . PHP_EOL .' . PHP_EOL .
            '        \'$ hookmeup install --composer-vendor-path=...\' . PHP_EOL' . PHP_EOL .
            '    );' . PHP_EOL .
            '    exit(1);' . PHP_EOL .
            '}' . PHP_EOL .
            'require $autoLoader;' . PHP_EOL .
            '$config = realpath(__DIR__ . \'/../../hookmeup.json\');' . PHP_EOL .
            '$app    = new HookMeUp\Console\Application\Hook();' . PHP_EOL .
            '$app->executeHook(\'' . $hook . '\')' . PHP_EOL .
            '    ->useConfigFile($config)' . PHP_EOL .
            '    ->run();' . PHP_EOL . PHP_EOL;

            $file = new File($hookFile);
            $file->write($code);
            chmod($hookFile, 0755);
            $this->io->write('    <info>\'' . $hook . '\' hook installed successfully</info>');
        }
    }
}
