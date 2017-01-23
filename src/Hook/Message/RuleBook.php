<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message;

use SebastianFeldmann\CaptainHook\Exception\ActionFailed;
use SebastianFeldmann\Git\CommitMessage;

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
     * @var \SebastianFeldmann\CaptainHook\Hook\Message\Rule[]
     */
    private $rules = [];

    /**
     * Set rules to check.
     *
     * @param  \SebastianFeldmann\CaptainHook\Hook\Message\Rule[] $rules
     * @return \SebastianFeldmann\CaptainHook\Hook\Message\Rulebook
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Add a rule to the list.
     *
     * @param  \SebastianFeldmann\CaptainHook\Hook\Message\Rule $rule
     * @return \SebastianFeldmann\CaptainHook\Hook\Message\Rulebook
     */
    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Validates all rules.
     *
     * @param  \SebastianFeldmann\Git\CommitMessage $msg
     * @throws \SebastianFeldmann\CaptainHook\Exception\ActionFailed
     */
    public function validate(CommitMessage $msg)
    {
        foreach ($this->rules as $rule) {
            if (!$rule->pass($msg)) {
                throw ActionFailed::withMessage($rule->getHint());
            }
        }
    }
}
