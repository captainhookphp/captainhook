<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Config;

/**
 * Class Hook
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Hook
{
    /**
     * Is hook enabled
     *
     * @var bool
     */
    private $isEnabled = false;

    /**
     * List of Actions
     *
     * @var \HookMeUp\App\Config\Action[]
     */
    private $actions = [];

    /**
     * Hook constructor.
     *
     * @param bool $enabled
     */
    public function __construct($enabled = false)
    {
        $this->isEnabled = $enabled;
    }

    /**
     * Enable or disable the hook.
     *
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->isEnabled = $enabled;
    }

    /**
     * Is this hook enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Add an action to the list.
     *
     * @param \HookMeUp\App\Config\Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    /**
     * Return the action list.
     *
     * @return \HookMeUp\App\Config\Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Return config data.
     *
     * @return array
     */
    public function getJsonData()
    {
        $config = ['enabled' => $this->isEnabled, 'actions' => []];
        foreach ($this->actions as $action) {
            $config['actions'][] = $action->getJsonData();
        }
        return $config;
    }
}
