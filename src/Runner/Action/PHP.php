<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Runner\Action;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Exception\ActionFailed;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Action;

/**
 * Class PHP
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class PHP implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionFailed
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $class = $action->getAction();

        try {
            if ($this->isStaticMethodCall($class)) {
                $io->write($this->executeStatic($class));
            } else {
                $exe = $this->createAction($class);
                $exe->execute($config, $io, $repository, $action);
            }
        } catch (\Exception $e) {
            throw ActionFailed::withMessage('Execution failed: ' . $e->getMessage());
        } catch (\Error $e) {
            throw ActionFailed::withMessage('PHP Error: ' . $e->getMessage());
        }
    }

    /**
     * Execute static method call and return its output.
     *
     * @param  string $class
     * @return string
     */
    protected function executeStatic($class)
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
        return ob_end_clean();
    }

    /**
     * Create an action instance.
     *
     * @param  string $class
     * @return \sebastianfeldmann\CaptainHook\Hook\Action
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionFailed
     */
    protected function createAction($class)
    {
        $action = new $class();
        if (!$action instanceof Action) {
            throw ActionFailed::withMessage(
                'PHP class ' . $class . ' has to implement the \'Action\' interface'
            );
        }
        return $action;
    }

    /**
     * Is this a static method call.
     *
     * @param  string $class
     * @return bool
     */
    protected function isStaticMethodCall($class)
    {
        return (bool)preg_match('#^\\\\.+::.+$#i', $class);
    }
}
