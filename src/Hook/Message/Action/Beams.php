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
use SebastianFeldmann\CaptainHook\Console\IOUtil;
use SebastianFeldmann\CaptainHook\Exception\ActionFailed;
use SebastianFeldmann\CaptainHook\Hook\Message\RuleBook;
use SebastianFeldmann\Cli\Output\Util as OutputUtil;
use SebastianFeldmann\Git\Repository;

/**
 * Class Beams
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Beams extends Book
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
        $options = $action->getOptions();
        $book    = new RuleBook();
        $book->setRules(RuleBook\RuleSet::beams(
            $options->get('subjectLength', 50),
            $options->get('bodyLineLength', 72)
        ));

        try {
            $this->validate($book, $repository);
        } catch (ActionFailed $exception) {
            $this->writeError($io, $repository);
            throw ActionFailed::fromPrevious($exception);
        }
    }

    /**
     * Write error to stdErr.
     *
     * @param \SebastianFeldmann\CaptainHook\Console\IO $io
     * @param \SebastianFeldmann\Git\Repository         $repository
     */
    private function writeError(IO $io, Repository $repository)
    {
        $io->writeError(array_merge(
            [
                '<error>COMMIT MESSAGE DOES NOT MEET THE REQUIREMENTS</error>',
                IOUtil::getLineSeparator(),
            ],
            OutputUtil::trimEmptyLines($repository->getCommitMsg()->getLines()),
            [IOUtil::getLineSeparator()]
        ));
    }
}
