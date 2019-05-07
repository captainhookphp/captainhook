<?php
declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hook\Util as HookUtil;

class Docker implements Template
{
    /**
     * @var string
     */
    private $binaryPath;
    /**
     * @var string
     */
    private $containerName;

    public function __construct(string $repoPath, string $vendorPath, string $containerName)
    {
        $this->binaryPath = HookUtil::getBinaryPath($repoPath, $vendorPath, 'captainhook-run');
        $this->containerName = $containerName;
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
        return '#!/usr/bin/env bash' . PHP_EOL .
            'docker exec ' . $this->containerName . ' ./' . $this->binaryPath . ' ' . $hook . ' "$@"';
    }
}