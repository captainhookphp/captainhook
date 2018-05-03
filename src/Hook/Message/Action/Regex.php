<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Hook\Message\Action;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO;
use SebastianFeldmann\CaptainHook\Exception\ActionFailed;
use SebastianFeldmann\CaptainHook\Hook\Action;
use SebastianFeldmann\Git\Repository;

/**
 * Class Regex
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.0.0
 */
class Regex implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \SebastianFeldmann\CaptainHook\Config         $config
     * @param  \SebastianFeldmann\CaptainHook\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \SebastianFeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $regex      = $this->getRegex($action->getOptions());
        $errorMsg   = $this->getErrorMessage($action->getOptions());
        $successMsg = $this->getSuccessMessage($action->getOptions());
        $matches    = [];

        if (!preg_match($regex, $repository->getCommitMsg()->getContent(), $matches)) {
            throw ActionFailed::withMessage(sprintf($errorMsg, $regex));
        }

        $io->write(sprintf($successMsg, $matches[0]));
    }

    /**
     * Extract regex from options array.
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Options $options
     * @return string
     * @throws \SebastianFeldmann\CaptainHook\Exception\ActionFailed
     */
    protected function getRegex(Config\Options $options)
    {
        $regex = $options->get('regex');
        if (empty($regex)) {
            throw ActionFailed::withMessage('No regex option');
        }
        return $regex;
    }

    /**
     * Determine the error message to use.
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Options $options
     * @return string
     */
    protected function getErrorMessage(Config\Options $options)
    {
        return $options->get('error') ?? 'Commit message did not match regex: %s';
    }

    /**
     * Determine the error message to use.
     *
     * @param  \SebastianFeldmann\CaptainHook\Config\Options $options
     * @return string
     */
    protected function getSuccessMessage(Config\Options $options)
    {
        return $options->get('success') ?? 'Found matching pattern: %s';
    }
}
