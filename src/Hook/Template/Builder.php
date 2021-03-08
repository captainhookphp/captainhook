<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Config;
use CaptainHook\App\Console\Runtime\Resolver;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hook\Template\Local\PHP;
use CaptainHook\App\Hook\Template\Local\Shell;
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
     * @param  \CaptainHook\App\Config                   $config
     * @param  \SebastianFeldmann\Git\Repository         $repository
     * @param  \CaptainHook\App\Console\Runtime\Resolver $resolver
     * @return \CaptainHook\App\Hook\Template
     */
    public static function build(Config $config, Repository $repository, Resolver $resolver): Template
    {
        $repositoryPath = self::toAbsolutePath($repository->getRoot());
        $configPath     = self::toAbsolutePath($config->getPath());
        $bootstrapPath  = dirname($configPath) . '/' . $config->getBootstrap();
        $captainPath    = self::toAbsolutePath($resolver->getExecutable());

        if (!file_exists($bootstrapPath)) {
            throw new RuntimeException('bootstrap file not found: \'' . $bootstrapPath . '\'');
        }

        $phpPath = $config->getPhpPath();

        switch ($config->getRunMode()) {
            case Template::DOCKER:
                return new Docker(
                    new Directory($repositoryPath),
                    new File($configPath),
                    new File($captainPath),
                    new Docker\Config($config->getRunExec(), $config->getRunPath())
                );
            case Template::PHP:
                return new PHP(
                    new Directory($repositoryPath),
                    new File($configPath),
                    new File($captainPath),
                    $config->getBootstrap(),
                    $resolver->isPharRelease(),
                    $phpPath
                );
            default:
                return new Shell(
                    new Directory($repositoryPath),
                    new File($configPath),
                    new File($captainPath),
                    $config->getBootstrap(),
                    $resolver->isPharRelease(),
                    $phpPath
                );
        }
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
