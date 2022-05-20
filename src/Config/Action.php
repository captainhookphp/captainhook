<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Config;

/**
 * Class Action
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
class Action
{
    /**
     * Action phpc lass or cli script
     *
     * @var string
     */
    private $action;

    /**
     * Map of options name => value
     *
     * @var \CaptainHook\App\Config\Options
     */
    private $options;

    /**
     * List of action conditions
     *
     * @var \CaptainHook\App\Config\Condition[]
     */
    private $conditions = [];

    /**
     * Action constructor
     *
     * @param  string               $action
     * @param  array<string, mixed> $options
     * @param  array<string, mixed> $conditions
     * @throws \Exception
     */
    public function __construct(string $action, array $options = [], array $conditions = [])
    {
        $this->action = $action;
        $this->setupOptions($options);
        $this->setupConditions($conditions);
    }

    /**
     * Setup options
     *
     * @param array<string, mixed> $options
     */
    private function setupOptions(array $options): void
    {
        $this->options = new Options($options);
    }

    /**
     * Setup action conditions
     *
     * @param array<string, array<string, mixed>> $conditions
     */
    private function setupConditions(array $conditions): void
    {
        foreach ($conditions as $condition) {
            $this->conditions[] = new Condition($condition['exec'], $condition['args'] ?? []);
        }
    }

    /**
     * Action getter
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Return option map
     *
     * @return \CaptainHook\App\Config\Options
     */
    public function getOptions(): Options
    {
        return $this->options;
    }

    /**
     * Return condition configurations
     *
     * @return \CaptainHook\App\Config\Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Return config data
     *
     * @return array<string, mixed>
     */
    public function getJsonData(): array
    {
        return [
            'action'     => $this->action,
            'options'    => $this->options->getAll(),
            'conditions' => $this->getConditionJsonData()
        ];
    }

    /**
     * Return conditions json data
     *
     * @return array<int, mixed>
     */
    private function getConditionJsonData(): array
    {
        $json = [];
        foreach ($this->conditions as $condition) {
            $json[] = $condition->getJsonData();
        }
        return $json;
    }
}
