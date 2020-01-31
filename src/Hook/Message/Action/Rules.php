<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Message\Rule;
use CaptainHook\App\Hook\Message\RuleBook;
use Exception;
use SebastianFeldmann\Git\Repository;

/**
 * Class Rules
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Rules extends Book
{
    /**
     * Execute the configured action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $rules = $action->getOptions()->getAll();
        $book  = new RuleBook();
        foreach ($rules as $class) {
            $book->addRule($this->createRule($class));
        }
        $this->validate($book, $repository, $io);
    }

    /**
     * Create a new rule.
     *
     * @param  string $class
     * @return \CaptainHook\App\Hook\Message\Rule
     * @throws \Exception
     */
    protected function createRule(string $class): Rule
    {
        // make sure the class is available
        if (!class_exists($class)) {
            throw new Exception('Unknown rule: ' . $class);
        }

        $rule = new $class();

        // make sure the class implements the Rule interface
        if (!$rule instanceof Rule) {
            throw new Exception('Class \'' . $class . '\' must implement the Rule interface');
        }

        return $rule;
    }
}
