<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Composer\Action;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Action;

/**
 * Class CheckLockFile
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.0.1
 */
class CheckLockFile implements Action
{
    /**
     * Executes the action.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $path           = $action->getOptions()->get('path', getcwd());
        $lockFileHash   = $this->getLockFileHash($path);
        $configFileHash = $this->getConfigFileHash($path);

        if ($lockFileHash !== $configFileHash) {
            throw new \Exception('composer.lock is out of date');
        }

        $io->write('<info>composer.lock is up to date</info>');
    }

    /**
     * Read the composer.lock file and extract the composer.json hash.
     *
     * @param  string $path
     * @return string
     */
    private function getLockFileHash($path)
    {
        $lockFile = json_decode($this->loadFile($path . DIRECTORY_SEPARATOR . 'composer.lock'));

        return $lockFile->hash;
    }

    /**
     * Read the composer.json file and create a md5 hash on its contents.
     *
     * @param  string $path
     * @return string
     */
    private function getConfigFileHash($path)
    {
        return md5($this->loadFile($path . DIRECTORY_SEPARATOR . 'composer.json'));
    }

    /**
     * Load a composer file.
     *
     * @param  string $file
     * @return \stdClass
     * @throws \Exception
     */
    private function loadFile($file)
    {
        if (!file_exists($file)) {
            throw new \Exception($file . ' not found');
        }
        return file_get_contents($file);
    }
}
