<?php
declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hook\Util as HookUtil;

class Local implements Template
{
    /**
     * @var string
     */
    private $vendorPath;
    /**
     * @var string
     */
    private $configPath;

    public function __construct(string $repoPath, string $vendorPath, string $configPath)
    {
        $this->vendorPath = HookUtil::getTplTargetPath($repoPath, $vendorPath);
        $this->configPath = HookUtil::getTplTargetPath($repoPath, $configPath);
    }

    /**
     * Return the code for the git hook scripts.
     *
     * @param string $hook Name of the hook to trigger.
     *
     * @return string
     */
    public function getCode(string $hook): string
    {
        return '#!/usr/bin/env php' . PHP_EOL .
            '<?php' . PHP_EOL .
            '$autoLoader = ' . $this->vendorPath . '/autoload.php\';' . PHP_EOL . PHP_EOL .
            'if (!file_exists($autoLoader)) {' . PHP_EOL .
            '    fwrite(STDERR,' . PHP_EOL .
            '        \'Composer autoload.php could not be found\' . PHP_EOL .' . PHP_EOL .
            '        \'Please re-install the hook with:\' . PHP_EOL .' . PHP_EOL .
            '        \'$ captainhook install --composer-vendor-path=...\' . PHP_EOL' . PHP_EOL .
            '    );' . PHP_EOL .
            '    exit(1);' . PHP_EOL .
            '}' . PHP_EOL .
            'require $autoLoader;' . PHP_EOL .
            '$config = realpath(' . $this->configPath . '\');' . PHP_EOL .
            '$app    = new CaptainHook\App\Console\Application\Hook();' . PHP_EOL .
            '$app->setHook(\'' . $hook . '\');' . PHP_EOL .
            '$app->setConfigFile($config);' . PHP_EOL .
            '$app->run();' . PHP_EOL . PHP_EOL;
    }
}