<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook;

/**
 * Class Config
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Config
{
    /**
     * Path to the config file
     *
     * @var string
     */
    private $path;

    /**
     * Does the config file exist
     *
     * @var bool
     */
    private $fileExists;

    /**
     * List of hook configs
     *
     * @var \SebastianFeldmann\CaptainHook\Config\Hook[]
     */
    private $hooks = [];

    /**
     * Config constructor.
     *
     * @param string $path
     * @param bool   $fileExists
     */
    public function __construct(string $path, bool $fileExists = false)
    {
        $this->path                = $path;
        $this->fileExists          = $fileExists;
        $this->hooks['commit-msg'] = new Config\Hook();
        $this->hooks['pre-commit'] = new Config\Hook();
        $this->hooks['pre-push']   = new Config\Hook();
    }

    /**
     * Is configuration loaded from file.
     *
     * @return bool
     */
    public function isLoadedFromFile() : bool
    {
        return $this->fileExists;
    }

    /**
     * Path getter.
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Return config for given hook.
     *
     * @param  string $hook
     * @return \SebastianFeldmann\CaptainHook\Config\Hook
     * @throws \InvalidArgumentException
     */
    public function getHookConfig(string $hook) : Config\Hook
    {
        if (!Hook\Util::isValid($hook)) {
            throw new \InvalidArgumentException('Invalid hook name: ' . $hook);
        }
        return $this->hooks[$hook];
    }

    /**
     * Return config array to write to disc.
     *
     * @return array
     */
    public function getJsonData() : array
    {
        return [
            'commit-msg' => $this->hooks['commit-msg']->getJsonData(),
            'pre-commit' => $this->hooks['pre-commit']->getJsonData(),
            'pre-push'   => $this->hooks['pre-push']->getJsonData()
        ];
    }
}
