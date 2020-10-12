<?php

declare(strict_types=1);

namespace CaptainHook\App\Hook\File\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Hook\Action;
use Exception;
use SebastianFeldmann\Git\Repository;

class NotEmpty implements Action
{
    /**
     * Execute the action.
     *
     * @param Config        $config
     * @param IO            $io
     * @param Repository    $repository
     * @param Config\Action $action
     *
     * @return void
     *
     * @throws Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action): void
    {
        $pathToFolder = $action->getOptions()->get('pathSrc');
        $searchedFolder = $action->getOptions()->get('folderName');
        $path     = glob((getcwd() . $pathToFolder));
        $errors   = [];
        foreach ($path as $folder) {
            $folderName = $folder . $searchedFolder;
            if (!is_dir($folderName)) {
                $errors[] = '<error>Missing ' . $searchedFolder . ' folder in ' . basename($folder) . ' </error>';
            }
            if (is_dir($folderName)) {
                $filesInDir = array_diff(scandir($folderName), ['.', '..']);
                if (count($filesInDir) === 0) {
                    $errors[] = '<error>Empty   ' . $searchedFolder . ' folder in ' . basename($folder) . ' </error>';
                }
            }
        }
        if (empty($errors)) {
            $io->write('<info>No errors found while checking for ' . $searchedFolder . ' files</info>');
        } else {
            $io->writeError($errors);
            throw new Exception('Please check if ' . $searchedFolder . ' folder is not missing and is not empty');
        }
    }
}
