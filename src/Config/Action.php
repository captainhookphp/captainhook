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
 * Class Action
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Action
{
    /**
     * Action type
     *
     * @var string
     */
    private $type;

    /**
     * Action phpc lass or cli script
     *
     * @var string
     */
    private $action;

    /**
     * Map of options name => value
     *
     * @var \SebastianFeldmann\CaptainHook\Config\Options
     */
    private $options;

    /**
     * List of valid action types
     *
     * @var array
     */
    protected static $validTypes = ['php' => true, 'cli' => true];

    /**
     * Action constructor.
     *
     * @param  string $type
     * @param  string $action
     * @param  array  $options
     * @throws \Exception
     */
    public function __construct(string $type, string $action, array $options = [])
    {
        if (!isset(self::$validTypes[$type])) {
            throw new \Exception(sprintf('Invalid action type: %s', $type));
        }
        $this->type    = $type;
        $this->action  = $action;
        $this->options = new Options($options);
    }

    /**
     * Type getter.
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Action getter.
     *
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * Return option map.
     *
     * @return \SebastianFeldmann\CaptainHook\Config\Options
     */
    public function getOptions() : Options
    {
        return $this->options;
    }

    /**
     * Return config data.
     *
     * @return array
     */
    public function getJsonData() : array
    {
        return [
            'action'  => $this->action,
            'options' => $this->options->getAll(),
        ];
    }
}
