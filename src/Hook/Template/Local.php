<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace CaptainHook\App\Hook\Template;

use CaptainHook\App\Hook\Template;
use CaptainHook\App\Storage\Util;
use SebastianFeldmann\Camino\Path;
use SebastianFeldmann\Camino\Path\Directory;
use SebastianFeldmann\Camino\Path\File;

/**
 * Local class
 *
 * Generates the sourcecode for the php hook scripts in .git/hooks/*.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 4.3.0
 */
class Local implements Template
{
    /**
     * Path to the vendor directory
     *
     * @var string
     */
    private $vendorPath;

    /**
     * Path to the captainhook configuration
     *
     * @var string
     */
    private $configPath;

    /**
     * Local constructor
     *
     * @param \SebastianFeldmann\Camino\Path\Directory $repo
     * @param \SebastianFeldmann\Camino\Path\Directory $vendor
     * @param \SebastianFeldmann\Camino\Path\File      $config
     */
    public function __construct(Directory $repo, Directory $vendor, File $config)
    {
        $this->vendorPath = $this->getPathFromHookTo($repo, $vendor);
        $this->configPath = $this->getPathFromHookTo($repo, $config);
    }

    /**
     * Return the code for the git hook scripts
     *
     * @param  string $hook Name of the hook to generate the sourcecode for
     * @return string
     */
    public function getCode(string $hook): string
    {
        return '#!/usr/bin/env php' . PHP_EOL .
            '<?php' . PHP_EOL .
            '$autoLoader = ' . $this->vendorPath . '/autoload.php\';' . PHP_EOL . PHP_EOL .
            'if (!file_exists($autoLoader)) {' . PHP_EOL .
            '    fwrite(STDERR, \'Composer autoload.php could not be found\');' . PHP_EOL .
            '    exit(1);' . PHP_EOL .
            '}' . PHP_EOL .
            'require $autoLoader;' . PHP_EOL .
            '$config = realpath(' . $this->configPath . '\');' . PHP_EOL .
            '$app    = new CaptainHook\App\Console\Application\Hook();' . PHP_EOL .
            '$app->setHook(\'' . $hook . '\');' . PHP_EOL .
            '$app->setConfigFile($config);' . PHP_EOL .
            '$app->setRepositoryPath(dirname(dirname(__DIR__)));' . PHP_EOL .
            '$app->run();' . PHP_EOL . PHP_EOL;
    }

    /**
     * Return the path to the target path from inside the .git/hooks directory f.e. __DIR__ ../../vendor
     *
     * @param  \SebastianFeldmann\Camino\Path\Directory $repo
     * @param  \SebastianFeldmann\Camino\Path           $target
     * @return string
     */
    private function getPathFromHookTo(Directory $repo, Path $target) : string
    {
        if (!$target->isChildOf($repo)) {
            return $target->getPath();
        }

        return '__DIR__ . \'/../../' . $target->getRelativePathFrom($repo);
    }
}
