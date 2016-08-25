<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook\Message;

use HookMeUp\Config;
use HookMeUp\Console\IO;
use HookMeUp\Git\Repository;
use HookMeUp\Hook\Action;

/**
 * Class Beams
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Beams implements Action
{
    /**
     * Execute the configured action.
     *
     * @param  \HookMeUp\Config         $config
     * @param  \HookMeUp\Console\IO     $io
     * @param  \HookMeUp\Git\Repository $repository
     * @param  \HookMeUp\Config\Action  $action
     * @throws \HookMeUp\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        // if this is no merge commit enforce message rules
        if (!$repository->isMerging()) {
            $validator = new Validator();
            $validator->setRules(
                [
                    new Validator\Rule\CapitalizeSubject(),
                    new Validator\Rule\LimitSubjectLength(50),
                    new Validator\Rule\NoPeriodOnSubjectEnd(),
                    new Validator\Rule\UseImperativeMood(),
                    new Validator\Rule\LimitBodyLineLength(),
                    new Validator\Rule\SeparateSubjectFromBodyWithBlankLine(),
                ]
            );

            $validator->validate($repository->getCommitMsg());
        }
    }
}
