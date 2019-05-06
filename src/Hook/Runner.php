<?php
declare(strict_types=1);

namespace CaptainHook\App\Hook;

use CaptainHook\App\Storage\Util as StorageUtil;

abstract class Runner
{
    /**
     * Return the php code for the git hook runner.
     *
     * @param string $repoPath   Absolute path to the repository root.
     * @param string $vendorPath Absolute path to the vendor folder.
     * @param string $configPath Absolute path to the configuration file.
     *
     * @return string
     */
    public static function getCode(string $repoPath, string $vendorPath, string $configPath): string
    {
        $tplVendorPath = self::getTplTargetPath($repoPath, $vendorPath);
        $tplConfigPath = self::getTplTargetPath($repoPath, $configPath);

        return '#!/usr/bin/env php' . PHP_EOL .
            '<?php' . PHP_EOL .
            '$autoLoader = ' . $tplVendorPath . '/autoload.php\';' . PHP_EOL . PHP_EOL .
            'if (!file_exists($autoLoader)) {' . PHP_EOL .
            '    fwrite(STDERR,' . PHP_EOL .
            '        \'Composer autoload.php could not be found\' . PHP_EOL .' . PHP_EOL .
            '        \'Please re-install the hook with:\' . PHP_EOL .' . PHP_EOL .
            '        \'$ captainhook install --composer-vendor-path=...\' . PHP_EOL' . PHP_EOL .
            '    );' . PHP_EOL .
            '    exit(1);' . PHP_EOL .
            '}' . PHP_EOL .
            '$hook = $_SERVER[\'argv\'][1];' . PHP_EOL .
            'array_splice($_SERVER[\'argv\'], 1, 1);' . PHP_EOL .
            'require $autoLoader;' . PHP_EOL .
            '$config = realpath(' . $tplConfigPath . '\');' . PHP_EOL .
            '$app    = new CaptainHook\App\Console\Application\Hook();' . PHP_EOL .
            '$app->setHook($hook);' . PHP_EOL .
            '$app->setConfigFile($config);' . PHP_EOL .
            '$app->run();' . PHP_EOL . PHP_EOL;
    }

    /**
     * Return the path to the target path from inside the .git/hooks directory f.e. __DIR__ ../../vendor.
     *
     * @param string $repoDir
     * @param string $targetPath
     *
     * @return string
     * @throws \RuntimeException
     */
    public static function getTplTargetPath(string $repoDir, string $targetPath): string
    {
        $repo = explode(DIRECTORY_SEPARATOR, ltrim($repoDir, DIRECTORY_SEPARATOR));
        $target = explode(DIRECTORY_SEPARATOR, ltrim($targetPath, DIRECTORY_SEPARATOR));

        if (!StorageUtil::isSubDirectoryOf($target, $repo)) {
            return '\'' . $targetPath;
        }

        return '__DIR__ . \'/../../' . StorageUtil::getSubPathOf($target, $repo);
    }
}