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

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Hook\Util as HookUtil;
use SebastianFeldmann\CaptainHook\Storage\File\Json;

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
     * @return \SebastianFeldmann\CaptainHook\Config
     */
    public static function create(string $path = '')
    {
        $factory = new static();

        return $factory->createConfig($path);
    }

    /**
     * Create a CaptainHook configuration.
     *
     * @param  string $path
     * @return \SebastianFeldmann\CaptainHook\Config
     * @throws \Exception
     */
    public function createConfig($path = '') : Config
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
     * @param  \SebastianFeldmann\CaptainHook\Config $config
     * @param  array                                 $json
     * @throws \Exception
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
     * @param  \SebastianFeldmann\CaptainHook\Config\Hook $config
     * @param  array                                      $json
     * @throws \Exception
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
