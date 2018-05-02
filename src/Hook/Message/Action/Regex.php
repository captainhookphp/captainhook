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
    const MESSAGE_ERROR = 'error';
    const MESSAGE_SUCCESS = 'success';

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
        $regex = $this->getRegex($action->getOptions());
        $messageSuccess = $this->getMessage($action->getOptions(), self::MESSAGE_SUCCESS);
        $messageError = $this->getMessage($action->getOptions(), self::MESSAGE_ERROR);

        if (!preg_match($regex, $repository->getCommitMsg()->getContent())) {
            throw ActionFailed::withMessage(sprintf(
                $messageError ?? 'Commit message did not match regex: %s',
                $regex
            ));
        } else if (!empty($messageSuccess)) {
            $io->write(sprintf($messageSuccess, $regex));
        }
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
     * Extract success/error message from options array.
     *
     * @param Config\Options $options
     * @param string $type
     * @return string|null
     */
    protected function getMessage(Config\Options $options, string $type)
    {
        $message = $options->get($type);
        if (empty($message)) {
            return null;
        }
        return $message;
    }
}
