<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Console\IOUtil;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Message\RuleBook;
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
     * Execute the configured action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action) : void
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
     * Write error to stdErr
     *
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @return void
     */
    private function writeError(IO $io, Repository $repository) : void
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
