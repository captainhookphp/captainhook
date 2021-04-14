<?php

/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action\Cli\Command\Placeholder;

use CaptainHook\App\Config;
use CaptainHook\App\Runner\Action\Cli\Command\Placeholder as PlaceholderInterface;
use SebastianFeldmann\Git\Repository;

/**
 * Class Foundation
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.6.0
 */
abstract class Foundation implements PlaceholderInterface
{
    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    protected $config;

    /**
     * Git repository
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    protected $repository;

    /**
     * StagedFile constructor
     *
     * @param \CaptainHook\App\Config           $config
     * @param \SebastianFeldmann\Git\Repository $repository
     */
    public function __construct(Config $config, Repository $repository)
    {
        $this->config     = $config;
        $this->repository = $repository;
    }
}
