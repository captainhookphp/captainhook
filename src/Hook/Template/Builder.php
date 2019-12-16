<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Config;
use CaptainHook\App\Hook\Template;
use RuntimeException;
use SebastianFeldmann\Camino\Check;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;
use SebastianFeldmann\Git\Repository;

/**
 * Builder class
 *
 * Creates git hook Template objects regarding some provided input.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.3.0
 */
abstract class Builder
{
    /**
     * Creates a template that is responsible for the git hook sourcecode
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  string                            $executable
     * @param  bool                              $isPhar
     * @return \CaptainHook\App\Hook\Template
     */
    public static function build(Config $config, Repository $repository, string $executable, bool $isPhar): Template
    {
        $repositoryPath = self::toAbsolutePath($repository->getRoot());
        $configPath     = self::toAbsolutePath($config->getPath());
        $bootstrapPath  = dirname($configPath) . '/' . $config->getBootstrap();
        $captainPath    = self::toAbsolutePath($executable);

        if (!file_exists($bootstrapPath)) {
            throw new RuntimeException('bootstrap file not found: \'' . $bootstrapPath . '\'');
        }

        if ($config->getRunMode() === Template::DOCKER) {
            return new Docker(
                new Directory($repositoryPath),
                new File($configPath),
                new File($captainPath),
                new Docker\Config($config->getRunExec(), $config->getRunPath()),
                $config->getBootstrap()
            );
        }

        return new Local(
            new Directory($repositoryPath),
            new File($configPath),
            new File($captainPath),
            $config->getBootstrap(),
            $isPhar
        );
    }

    /**
     * Make sure the given path is absolute
     *
     * @param  string $path
     * @return string
     */
    private static function toAbsolutePath(string $path): string
    {
        if (Check::isAbsolutePath($path)) {
            return $path;
        }
        return (string) realpath($path);
    }
}
