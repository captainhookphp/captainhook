<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace sebastianfeldmann\CaptainHook\Hook\Message\Check;

use sebastianfeldmann\CaptainHook\Config;
use sebastianfeldmann\CaptainHook\Console\IO;
use sebastianfeldmann\CaptainHook\Git\Repository;
use sebastianfeldmann\CaptainHook\Hook\Message\Rule;
use sebastianfeldmann\CaptainHook\Hook\Message\RuleBook;

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
     * @param  \sebastianfeldmann\CaptainHook\Config         $config
     * @param  \sebastianfeldmann\CaptainHook\Console\IO     $io
     * @param  \sebastianfeldmann\CaptainHook\Git\Repository $repository
     * @param  \sebastianfeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $options = $action->getOptions();
        $book    = new RuleBook();
        $book->setRules(
            [
                new Rule\CapitalizeSubject(),
                new Rule\LimitSubjectLength($options->get('subjectLength', 50)),
                new Rule\NoPeriodOnSubjectEnd(),
                new Rule\UseImperativeMood(),
                new Rule\LimitBodyLineLength($options->get('bodyLineLength', 72)),
                new Rule\SeparateSubjectFromBodyWithBlankLine(),
            ]
        );

        $this->validate($book, $repository);
    }
}
