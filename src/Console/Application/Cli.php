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

use CaptainHook\App\Console\Application;
use CaptainHook\App\Console\Command as Cmd;
use CaptainHook\App\Console\Runtime\Resolver;

/**
 * Class Cli
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
class Cli extends Application
{
    /**
     * Path to captainhook binary
     *
     * @var string
     */
    protected $executable;

    /**
     * Cli constructor.
     *
     * @param string $executable
     */
    public function __construct(string $executable)
    {
        $this->executable = $executable;

        parent::__construct();
    }

    /**
     * Initializes all the CaptainHook commands
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    public function getDefaultCommands(): array
    {
        $resolver = new Resolver();

        return array_merge(
            parent::getDefaultCommands(),
            [
                new Cmd\Install($resolver, $this->executable),
                new Cmd\Configuration(),
                new Cmd\Add(),
                new Cmd\Disable(),
                new Cmd\Enable(),
                new Cmd\Hook\CommitMsg($resolver),
                new Cmd\Hook\PostCheckout($resolver),
                new Cmd\Hook\PostCommit($resolver),
                new Cmd\Hook\PostMerge($resolver),
                new Cmd\Hook\PreCommit($resolver),
                new Cmd\Hook\PrepareCommitMsg($resolver),
                new Cmd\Hook\PrePush($resolver),
            ]
        );
    }
}
