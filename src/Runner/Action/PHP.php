<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use CaptainHook\App\Hook\ActionFactory;
use SebastianFeldmann\Git\Repository;

/**
 * Class PHP
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class PHP
{
    /**
     * Execute the configured action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action) : void
    {
        $class = $action->getAction();

        try {
            if ($this->isStaticMethodCall($class)) {
                $io->write($this->executeStatic($class));
                return;
            }

            $exe = $this->createAction($class);
            $exe->execute($config, $io, $repository, $action);
        } catch (\Exception $e) {
            throw new ActionFailed(
                'Execution failed: ' . PHP_EOL .
                $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()
            );
        } catch (\Error $e) {
            throw new ActionFailed('PHP Error:' . $e->getMessage());
        }
    }

    /**
     * Execute static method call and return its output
     *
     * @param  string $class
     * @return string
     */
    protected function executeStatic(string $class) : string
    {
        list($class, $method) = explode('::', $class);
        if (!class_exists($class)) {
            throw new \RuntimeException('could not find class: ' . $class);
        }
        if (!method_exists($class, $method)) {
            throw new \RuntimeException('could not find method in class: ' . $method);
        }
        ob_start();
        $class::$method();
        return (string)ob_get_clean();
    }

    /**
     * Create an action instance
     *
     * @param  string $class
     * @return \CaptainHook\App\Hook\Action
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    protected function createAction(string $class) : Action
    {
        $action = new $class();
        if (!$action instanceof Action) {
            throw new ActionFailed(
                'PHP class ' . $class . ' has to implement the \'Action\' interface'
            );
        }
        return $action;
    }

    /**
     * Is this a static method call
     *
     * @param  string $class
     * @return bool
     */
    protected function isStaticMethodCall(string $class) : bool
    {
        return (bool)preg_match('#^\\\\.+::.+$#i', $class);
    }
}
