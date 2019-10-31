<?php
/**
 * This file is part of CaptainHook.
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
use CaptainHook\App\Storage\Util;
use RuntimeException;
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
     * Creates a template that is responsible for the git hook template
     *
     * @param  \CaptainHook\App\Config                         $config
     * @param  \SebastianFeldmann\Git\Repository               $repository
     * @return \CaptainHook\App\Hook\Template
     */
    public static function build(Config $config, Repository $repository): Template
    {
        $vendorPath     = (string) realpath($config->getVendorDirectory());
        $repositoryPath = (string) realpath($repository->getRoot());
        $configPath     = (string) realpath($config->getPath());

        if (empty($vendorPath)) {
            throw new RuntimeException('composer vendor directory not found');
        }

        if ($config->getRunMode() === Template::DOCKER) {
            // For docker we need to strip down the current working directory.
            // This is caused because docker will always connect to a specific working directory
            // where the absolute path will not be recognized.
            // E.g.:
            //   cwd => /docker
            //   path => /docker/captainhook-run
            // The actual path needs to be /captainhook-run to work
            $dockerRepoPath = self::getRelativePath($repositoryPath);

            return new Docker(
                $dockerRepoPath,
                $config->getVendorDirectory(),
                $config->getRunExec()
            );
        }

        return new Local(
            $repositoryPath,
            $vendorPath,
            $configPath
        );
    }

    /**
     * Transforms an absolute path to a relative one
     *
     * @param  string $path
     * @return string
     */
    private static function getRelativePath(string $path): string
    {
        return Util::getSubPathOf(Util::pathToArray($path), Util::pathToArray((string)getcwd()));
    }
}
