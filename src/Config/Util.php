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

use SebastianFeldmann\CaptainHook\Hook\Util as HookUtil;

/**
 * Class Util
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.0.3
 */
abstract class Util
{
    /**
     * Validate a configuration.
     *
     * @param  array $json
     * @throws \RuntimeException
     */
    public static function validateJsonConfiguration(array $json)
    {
        foreach ($json as $hook => $config) {
            // check hook name
            if (!HookUtil::isValid($hook)) {
                throw new \RuntimeException('Config error: invalid hook \'' . $hook . '\'');
            }
            self::validateHookConfig($config);
        }
    }

    /**
     * Validate a hook configuration.
     *
     * @param  array $json
     * @throws \RuntimeException
     */
    public static function validateHookConfig(array $json)
    {
        if (!self::keysExist(['enabled', 'actions'], $json)) {
            throw new \RuntimeException('Config error: invalid hook configuration');
        }
        if (!is_array($json['actions'])) {
            throw new \RuntimeException('Config error: \'actions\' must be an array');
        }
        self::validateActionsConfig($json['actions']);
    }

    /**
     * Validate a list of action configurations.
     *
     * @param  array $json
     * @throws \RuntimeException
     */
    public static function validateActionsConfig(array $json)
    {
        foreach ($json as $action) {
            if (!self::keysExist(['action'], $action)) {
                throw new \RuntimeException('Config error: \'action\' missing');
            }
            if (empty($action['action'])) {
                throw new \RuntimeException('Config error: \'action\' can\'t be empty');
            }
        }
    }

    /**
     * Does an array have the expected keys.
     *
     * @param  array $keys
     * @param  array $subject
     * @return bool
     */
    private static function keysExist(array $keys, array $subject) : bool
    {
        foreach ($keys as $key) {
            if (!isset($subject[$key])) {
                return false;
            }
        }
        return true;
    }
}
