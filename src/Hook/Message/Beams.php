<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Git\Repository;

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
     * @param  \CaptainHook\App\Config         $config
     * @param  \CaptainHook\App\Console\IO     $io
     * @param  \CaptainHook\App\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action  $action
     * @throws \CaptainHook\App\Exception\ActionExecution
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
