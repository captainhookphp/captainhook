<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Exception\ActionFailed;
use CaptainHook\App\Hook\Action;
use Exception;
use SebastianFeldmann\Git\Repository;

/**
 * Class IsNotEmpty
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.4.1
 */
class IsNotEmpty extends Check
{
    /**
     * Actual action name for better error messages
     *
     * @var string
     */
    protected $actionName = 'IsNotEmpty';

    /**
     * Executes the action
     *
     * @param  \CaptainHook\App\Config           $config
     * @param  \CaptainHook\App\Console\IO       $io
     * @param  \SebastianFeldmann\Git\Repository $repository
     * @param  \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $filesStaged = $repository->getIndexOperator()->getStagedFiles();
        $filesFailed = 0;

        foreach ($this->getFilesToCheck($action->getOptions(), $filesStaged) as $stagedFileToCheck) {
            if ($this->isEmpty($stagedFileToCheck)) {
                $io->write('- <error>FAIL</error> ' . $stagedFileToCheck, true);
                $filesFailed++;
            } else {
                $io->write('- <info>OK</info> ' . $stagedFileToCheck, true, IO::VERBOSE);
            }
        }

        if ($filesFailed > 0) {
            throw new ActionFailed('<error>Error: ' . $filesFailed . ' empty file(s)</error>');
        }

        $io->write('<info>None of the checked files is empty</info>');
    }
}
