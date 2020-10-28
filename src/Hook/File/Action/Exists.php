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
use SebastianFeldmann\Git\Repository;

/**
 * Exists (in repository)
 *
 * This hook makes sure that a configured list of files exist in the repository.
 * For example you can use this to make sure you have committed some unit tests
 * before pushing your changes.
 *
 * {
 *     "action": "\\CaptainHook\\App\\Hook\\File\\Action\\Exists",
 *     "options": {
 *         "files" : [
 *             "tests/CaptainHook/ ** / * Test.php",
 *             "README.md"
 *         ]
 *     }
 * }
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.4.1
 */
class Exists implements Action
{
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
        $filesFailed  = 0;
        $filesToCheck = $this->getFilesToCheck($action->getOptions());

        foreach ($filesToCheck as $filesThatShouldExistInRepo) {
            $repoFiles = $repository->getInfoOperator()->getFilesInTree($filesThatShouldExistInRepo);
            if (empty($repoFiles)) {
                $filesFailed++;
                $io->write('- <error>FAIL</error> ' . $filesThatShouldExistInRepo, true);
            } else {
                $io->write('- <info>OK</info> ' . $filesThatShouldExistInRepo, true, IO::VERBOSE);
            }
        }

        if ($filesFailed > 0) {
            throw new ActionFailed('<error>Error: ' . $filesFailed . ' file(s) where not found</error>');
        }

        $io->write('<info>All files exist</info>');
    }

    /**
     * Retrieve configured file globs
     *
     * @param  \CaptainHook\App\Config\Options $options
     * @return array
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    private function getFilesToCheck(Config\Options $options): array
    {
        $files = $options->get('files', []);
        if (!is_array($files) || empty($files)) {
            throw new ActionFailed('no files configured');
        }
        return $files;
    }
}
