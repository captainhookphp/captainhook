<?php
/**
 * This file is part of SebastianFeldmann\Git.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Condition as ConditionInterface;
use CaptainHook\App\Hook\Condition\Cli;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;
use RuntimeException;

/**
 * Class Condition
 *
 * Executes an action condition by creating a condition object from a condition configuration.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.2.0
 */
class Condition
{
    /**
     * @var \CaptainHook\App\Console\IO
     */
    private $io;

    /**
     * @var \SebastianFeldmann\Git\Repository
     */
    private $repository;

    /**
     * Condition constructor.
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \SebastianFeldmann\Git\Repository $repository
     */
    public function __construct(IO $io, Repository $repository)
    {
        $this->io         = $io;
        $this->repository = $repository;
    }

    /**
     * Creates the configured condition and evaluates it
     *
     * @param  \CaptainHook\App\Config\Condition $config
     * @return bool
     */
    public function doesConditionApply(Config\Condition $config) : bool
    {
        $condition = $this->createCondition($config);
        return $condition->isTrue($this->io, $this->repository);
    }

    /**
     * Return the configured condition
     *
     * In case of a cli condition it returns an special condition class that deals with
     * the binary execution with implementing the same interface.
     *
     * @param  \CaptainHook\App\Config\Condition $config
     * @return \CaptainHook\App\Hook\Condition
     * @throws \RuntimeException
     */
    private function createCondition(Config\Condition $config) : ConditionInterface
    {
        if (Util::getExecType($config->getExec()) === 'cli') {
            return new Cli(new Processor(), $config->getExec());
        }

        $class = $config->getExec();
        if (!class_exists($class)) {
            throw new RuntimeException('could not find condition class: ' . $class);
        }
        return new $class(...$config->getArgs());
    }
}
