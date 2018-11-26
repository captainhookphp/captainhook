<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message;

use CaptainHook\App\Exception\ActionFailed;
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
     * @var \CaptainHook\App\Hook\Message\Rule[]
     */
    private $rules = [];

    /**
     * Set rules to check.
     *
     * @param  \CaptainHook\App\Hook\Message\Rule[] $rules
     * @return \CaptainHook\App\Hook\Message\RuleBook
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Add a rule to the list.
     *
     * @param  \CaptainHook\App\Hook\Message\Rule $rule
     * @return \CaptainHook\App\Hook\Message\RuleBook
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
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function validate(CommitMessage $msg)
    {
        $problems = [];
        // collect problems for all rules
        foreach ($this->rules as $rule) {
            if (!$rule->pass($msg)) {
                $problems[] = $rule->getHint();
            }
        }

        // any problems found?
        if (count($problems) > 0) {
            throw ActionFailed::withMessage($this->getOutput($problems));
        }
    }

    /**
     * Format the error output.
     *
     * @param  array $problems
     * @return string
     */
    private function getOutput(array $problems) : string
    {
        $output = 'RULEBOOK' . PHP_EOL .
                  '---------------------------------------------------------------------------' . PHP_EOL .
                  'CAPTAINHOOK FOUND ' . count($problems) . ' PROBLEMS(S) WITH YOUR COMMIT MESSAGE' . PHP_EOL .
                  '---------------------------------------------------------------------------' . PHP_EOL;

        foreach ($problems as $problem) {
            $output .= '  ' . $this->formatProblem($problem) . PHP_EOL;
        }

        $output .= '---------------------------------------------------------------------------' . PHP_EOL;

        return $output;
    }

    /**
     * Indent multi line problems so the lines after the first one are indented for better readability.
     *
     * @param  string $problem
     * @return string
     */
    private function formatProblem(string $problem) : string
    {
        $lines  = explode(PHP_EOL, $problem);
        $amount = count($lines);

        for ($i = 1; $i < $amount; $i++) {
            $lines[$i] = '    ' . $lines[$i];
        }

        return implode(PHP_EOL, $lines);


    }
}
