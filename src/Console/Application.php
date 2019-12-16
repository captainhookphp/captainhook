<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console;

use CaptainHook\App\CH;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Class Application
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Application extends SymfonyApplication
{
    /**
     * Cli constructor
     */
    public function __construct()
    {
        parent::__construct('CaptainHook', CH::VERSION);

        $this->setDefaultCommand('list');
        $this->silenceXDebug();
    }

    /**
     * Append release date to version output
     *
     * @return string
     */
    public function getLongVersion(): string
    {
        return sprintf(
            '<info>%s</info> version <comment>%s</comment> %s',
            $this->getName(),
            $this->getVersion(),
            CH::RELEASE_DATE
        );
    }

    /**
     * Make sure X-Debug does not interfere with the exception handling
     *
     * @return void
     */
    public function silenceXDebug(): void
    {
        if (function_exists('ini_set') && extension_loaded('xdebug')) {
            ini_set('xdebug.show_exception_trace', '0');
            ini_set('xdebug.scream', '0');
        }
    }
}
