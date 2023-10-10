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
        $pathInfo       = new PathInfo($repository->getRoot(), $config->getPath(), $resolver->getExecutable());
        $bootstrapPath  = dirname($config->getPath()) . '/' . $config->getBootstrap();

        if (!file_exists($bootstrapPath)) {
            throw new RuntimeException('bootstrap file not found: \'' . $bootstrapPath . '\'');
        }

        switch ($config->getRunConfig()->getMode()) {
            case Template::DOCKER:
                return new Docker(
                    $pathInfo,
                    $config
                );
            case Template::PHP:
                return new PHP(
                    $pathInfo,
                    $config,
                    $resolver->isPharRelease()
                );
            default:
                return new Shell(
                    $pathInfo,
                    $config,
                    $resolver->isPharRelease()
                );
        }
    }
}
