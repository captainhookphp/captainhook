<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Application;

use CaptainHook\App\Console\Command\Configuration;
use CaptainHook\App\Console\Command\Install;
use CaptainHook\App\Console\Runtime\Resolver;
use Composer\IO\IOInterface;
use CaptainHook\App\Console\Application as ConsoleApplication;
use CaptainHook\App\Console\IO\ComposerIO;

/**
 * Class Application
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Composer extends ConsoleApplication
{
    /**
     * Composer Application constructor
     *
     * This is private so you have to use the 'create' method to setup it up.
     */
    private function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a minimalistic composer application utilizing the composer IOInterface
     *
     * @param  \Composer\IO\IOInterface $io
     * @return \CaptainHook\App\Console\Application\Composer
     */
    public static function create(IOInterface $io): Composer
    {
        $proxyIO = new ComposerIO($io);
        $app     = new self();

        $install = new Install(new Resolver());
        $install->setIO($proxyIO);
        $app->add($install);

        $configuration = new Configuration();
        $configuration->setIO($proxyIO);
        $app->add($configuration);

        $app->setAutoExit(false);

        return $app;
    }
}
