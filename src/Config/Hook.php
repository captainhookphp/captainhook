<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Config;

/**
 * Class Hook
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
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
     * @var \SebastianFeldmann\CaptainHook\Config\Action[]
     */
    private $actions = [];

    /**
     * Hook constructor.
     *
     * @param bool $enabled
     */
    public function __construct(bool $enabled = false)
    {
        $this->isEnabled = $enabled;
    }

    /**
     * Enable or disable the hook.
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled)
    {
        $this->isEnabled = $enabled;
    }

    /**
     * Is this hook enabled.
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->isEnabled;
    }

    /**
     * Add an action to the list.
     *
     * @param \SebastianFeldmann\CaptainHook\Config\Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[] = $action;
    }

    /**
     * Return the action list.
     *
     * @return \SebastianFeldmann\CaptainHook\Config\Action[]
     */
    public function getActions() : array
    {
        return $this->actions;
    }

    /**
     * Return config data.
     *
     * @return array
     */
    public function getJsonData() : array
    {
        $config = ['enabled' => $this->isEnabled, 'actions' => []];
        foreach ($this->actions as $action) {
            $config['actions'][] = $action->getJsonData();
        }
        return $config;
    }
}
