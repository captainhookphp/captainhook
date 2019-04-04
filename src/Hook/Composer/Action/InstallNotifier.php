<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Composer\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use SebastianFeldmann\Git\Repository;

/**
 * Class InstallNotifier
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.1.1
 */
class InstallNotifier implements Action
{
    /**
     * Executes the action.
     *
     * @param \CaptainHook\App\Config           $config
     * @param \CaptainHook\App\Console\IO       $io
     * @param \SebastianFeldmann\Git\Repository $repository
     * @param \CaptainHook\App\Config\Action    $action
     * @return void
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $changedFiles = $this->getChangedFiles($io, $repository);

        print_r($changedFiles);
    }

    private function getChangedFiles(IO $io, Repository $repository)
    {
        $oldHash = $io->getArgument('previousHead');
        $newHash = $io->getArgument('newHead');

        if (!empty($oldHash) && !empty($newHash)) {
            return $repository->getLogOperator()->getChangedFilesSince($oldHash);
        }

        return $repository->getLogOperator()->getChangedFilesSince('HEAD@{1}');
    }
}
