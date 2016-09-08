<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Config;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Hook\Util as HookUtil;
use sebastianfeldmann\CaptainHook\Storage\File\Json;

/**
 * Class Factory
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Factory
{
    /**
     * Config factory method.
     *
     * @param  string $path
     * @return \sebastianfeldmann\CaptainHook\Config
     */
    public static function create($path = null)
    {
        $factory = new static();

        return $factory->createConfig($path);
    }

    /**
     * Create a CaptainHook configuration.
     *
     * @param  string $path
     * @return \sebastianfeldmann\CaptainHook\Config
     */
    public function createConfig($path = null)
    {
        $path       = $path ?: getcwd() . DIRECTORY_SEPARATOR . 'captainhook.json';
        $json       = new Json($path);
        $fileExists = $json->exists();
        $config     = new Config($path, $fileExists);

        if ($fileExists) {
            $this->configure($config, $json->readAssoc());
        }

        return $config;
    }

    /**
     * Initialize the configuration with data load from config file.
     *
     * @param \sebastianfeldmann\CaptainHook\Config $config
     * @param array                                 $json
     */
    protected function configure(Config $config, array $json)
    {
        Util::validateJsonConfiguration($json);

        foreach ($json as $hook => $data) {
            $this->configureHook($config->getHookConfig($hook), $data);
        }
    }

    /**
     * Setup a hook configuration by json data.
     *
     * @param \sebastianfeldmann\CaptainHook\Config\Hook $config
     * @param array                                      $json
     */
    protected function configureHook(Config\Hook $config, array $json)
    {
        $config->setEnabled($json['enabled']);
        foreach ($json['actions'] as $actionJson) {
            $type    = HookUtil::getActionType($actionJson['action']);
            $options = isset($actionJson['options']) && is_array($actionJson['options']) ? $actionJson['options'] : [];
            $config->addAction(new Config\Action($type, $actionJson['action'], $options));
        }
    }
}
