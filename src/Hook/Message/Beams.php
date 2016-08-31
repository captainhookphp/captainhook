<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\Hook\Message;

use CaptainHook\Config;
use CaptainHook\Console\IO;
use CaptainHook\Git\Repository;

/**
 * Class Beams
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/captainhook
 * @since   Class available since Release 0.9.0
 */
class Beams extends Base
{
    /**
     * Execute the configured action.
     *
     * @param  \CaptainHook\Config         $config
     * @param  \CaptainHook\Console\IO     $io
     * @param  \CaptainHook\Git\Repository $repository
     * @param  \CaptainHook\Config\Action  $action
     * @throws \CaptainHook\Exception\ActionExecution
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
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

        $this->executeValidator($validator, $repository);
    }
}
