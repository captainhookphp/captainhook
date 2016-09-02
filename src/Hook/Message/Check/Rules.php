<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message\Check;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Message\Rule;
use sebastianfeldmann\CaptainHook\Hook\Message\RuleBook;

/**
 * Class Rules
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Rules extends Book
{
    /**
     * Execute the configured action.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $rules = $action->getOptions()->getAll();
        $book  = new RuleBook();
        foreach ($rules as $class) {
            $book->addRule($this->createRule($class));
        }
        $this->validate($book, $repository);
    }

    /**
     * Create a new rule.
     *
     * @param  string $class
     * @return \sebastianfeldmann\CaptainHook\Hook\Message\Rule
     * @throws \Exception
     */
    protected function createRule($class)
    {
        // make sure the class is available
        if (!class_exists($class)) {
            throw new \Exception('Unknown rule: ' . $class);
        }

        $rule = new $class();

        // make sure the class implements the Rule interface
        if (!$rule instanceof Rule) {
            throw new \Exception('Class \'' . $class . '\' must implement the Rule interface');
        }

        return $rule;
    }
}
