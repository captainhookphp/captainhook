<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Exception\ActionExecution;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Action;

/**
 * Class RegexCheck
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 1.0.0
 */
class RegexCheck implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $regex = $this->getRegex($action->getOptions());

        if (!preg_match($regex, $repository->getCommitMsg()->getContent())) {
            throw new ActionExecution('Commit message did not match regex: ' . $regex);
        }
    }

    /**
     * Extract regex from options array.
     *
     * @param  array $options
     * @return string
     * @throws \sebastianfeldmann\CaptainHook\Exception\ActionExecution
     */
    protected function getRegex(array $options)
    {
        if (!isset($options['regex'])) {
            throw new ActionExecution('No regex option');
        }
        return $options['regex'];
    }
}
