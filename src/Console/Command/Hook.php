<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Command;

/**
 * Class Hook
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
abstract class Hook extends Base
{
    /**
     * Path to the configuration file to use.
     *
     * @var string
     */
    protected $configFile;

    /**
     * Path to the git repository to use.
     *
     * @var string
     */
    protected $repositoryPath;

    /**
     * Hook constructor.
     *
     * @param string $configFile
     * @param string $repositoryPath
     */
    public function __construct($configFile, $repositoryPath)
    {
        $this->configFile     = $configFile;
        $this->repositoryPath = $repositoryPath;
        parent::__construct();
    }
}
