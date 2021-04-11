<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Runner;
use SebastianFeldmann\Git\Repository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class HookHandler
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class RepositoryAware extends Runner
{
    /**
     * Git repository.
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    protected $repository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * HookHandler constructor.
     *
     * @param \CaptainHook\App\Console\IO       $io
     * @param \CaptainHook\App\Config           $config
     * @param \SebastianFeldmann\Git\Repository $repository
     * @param Filesystem|null                   $filesystem
     */
    public function __construct(
        IO $io,
        Config $config,
        Repository $repository,
        ?Filesystem $filesystem = null
    ) {
        parent::__construct($io, $config);
        $this->repository = $repository;
        $this->filesystem = $filesystem ?? new Filesystem();
    }
}
