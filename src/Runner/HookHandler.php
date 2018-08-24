<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Runner;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO;
use SebastianFeldmann\CaptainHook\Exception;
use SebastianFeldmann\CaptainHook\Hook\Util as HookUtil;
use SebastianFeldmann\CaptainHook\Runner;
use SebastianFeldmann\Git\Repository;

/**
 * Class HookHandler
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class HookHandler extends Runner
{
    /**
     * Git repository.
     *
     * @var \SebastianFeldmann\Git\Repository
     */
    protected $repository;

    /**
     * Hook that should be handled.
     *
     * @var string
     */
    protected $hookToHandle;

    /**
     * HookHandler constructor.
     *
     * @param \SebastianFeldmann\CaptainHook\Console\IO     $io
     * @param \SebastianFeldmann\CaptainHook\Config         $config
     * @param \SebastianFeldmann\Git\Repository $repository
     */
    public function __construct(IO $io, Config $config, Repository $repository)
    {
        parent::__construct($io, $config);
        $this->repository = $repository;
    }
    /**
     * Hook setter.
     *
     * @param  string $hook
     * @return \SebastianFeldmann\CaptainHook\Runner\HookHandler
     * @throws \SebastianFeldmann\CaptainHook\Exception\InvalidHookName
     */
    public function setHook(string $hook) : HookHandler
    {
        if (!empty($hook) && !HookUtil::isValid($hook)) {
            throw new Exception\InvalidHookName('Invalid hook name \'' . $hook . '\'');
        }
        $this->hookToHandle = $hook;
        return $this;
    }
}
