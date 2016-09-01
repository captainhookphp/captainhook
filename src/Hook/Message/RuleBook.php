<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message;

use sebastianfeldmann\CaptainHook\Exception\ActionExecution;
use sebastianfeldmann\CaptainHook\Git\CommitMessage;

/**
 * Class RuleBook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class RuleBook
{
    /**
     * List of rules to check
     *
     * @var \sebastianfeldmann\CaptainHook\Hook\Message\Rule[]
     */
    private $rules = [];

    /**
     * Set rules to check.
     *
     * @param  \sebastianfeldmann\CaptainHook\Hook\Message\Rule[] $rules
     * @return \sebastianfeldmann\CaptainHook\Hook\Message\Rulebook
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Add a rule to the list.
     *
     * @param  \sebastianfeldmann\CaptainHook\Hook\Message\Rule $rule
     * @return \sebastianfeldmann\CaptainHook\Hook\Message\Rulebook
     */
    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Validates all rules.
     *
     * @param  \sebastianfeldmann\CaptainHook\Git\CommitMessage $msg
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionExecution
     */
    public function validate(CommitMessage $msg)
    {
        foreach ($this->rules as $rule) {
            if (!$rule->pass($msg)) {
                throw new ActionExecution($rule->getHint());
            }
        }
    }
}
