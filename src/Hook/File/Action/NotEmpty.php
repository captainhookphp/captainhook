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
        $pathSrc  = $action->getOptions()->get('pathSrc');
        $fileName = $action->getOptions()->get('fileName');
        $path     = glob((getcwd() . $pathSrc));
        $errors   = [];
        foreach ($path as $file) {
            $readMeFile = $file . $fileName;
            if (!file_exists($readMeFile)) {
                $errors[] = '<error>Missing ' . $fileName . ' file in ' . basename($file) . ' </error>';
            }
            if (file_exists($readMeFile) && !file_get_contents($readMeFile)) {
                $errors[] = '<error>Empty ' . $fileName . ' file found in ' . basename($file) . ' </error>';
            }
        }
        if (empty($errors)) {
            $io->write('<info>No errors found while checking for ' . $fileName . ' files</info>');
        } else {
            $io->writeError($errors);
            throw new Exception('Please check if ' . $fileName . ' is not missing and is not empty');
        }
    }
}
