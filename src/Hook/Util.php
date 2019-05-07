<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook;

use CaptainHook\App\Hooks;
use CaptainHook\App\Storage\Util as StorageUtil;
use RuntimeException;

/**
 * Class Util
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Util
{
    /**
     * Checks if a hook name is valid
     *
     * @param  string $hook
     * @return bool
     */
    public static function isValid(string $hook) : bool
    {
        return isset(Hooks::getValidHooks()[$hook]);
    }

    /**
     * Returns list of valid hooks
     *
     * @return array
     */
    public static function getValidHooks() : array
    {
        return Hooks::getValidHooks();
    }

    /**
     * Returns hooks command class
     *
     * @param  string $hook
     * @return string
     */
    public static function getHookCommand(string $hook) : string
    {
        if (!self::isValid($hook)) {
            throw new RuntimeException(sprintf('Hook \'%s\' is not supported', $hook));
        }
        return Hooks::getValidHooks()[$hook];
    }

    /**
     * Get a list of all supported hooks
     *
     * @return array
     */
    public static function getHooks() : array
    {
        return array_keys(Hooks::getValidHooks());
    }

    /**
     * Return the path to the target path from inside the .git/hooks directory f.e. __DIR__ ../../vendor.
     *
     * @param string $repoDir
     * @param string $targetPath
     *
     * @return string
     * @throws RuntimeException
     */
    public static function getTplTargetPath(string $repoDir, string $targetPath) : string
    {
        $repo = explode(DIRECTORY_SEPARATOR, ltrim($repoDir, DIRECTORY_SEPARATOR));
        $target = explode(DIRECTORY_SEPARATOR, ltrim($targetPath, DIRECTORY_SEPARATOR));

        if (!StorageUtil::isSubDirectoryOf($target, $repo)) {
            return '\'' . $targetPath;
        }

        return '__DIR__ . \'/../../' . StorageUtil::getSubPathOf($target, $repo);
    }

    public static function getBinaryPath(string $repoDir, string $vendorPath, string $binary): string
    {
        $repo = explode(DIRECTORY_SEPARATOR, ltrim($repoDir, DIRECTORY_SEPARATOR));
        $vendor = explode(DIRECTORY_SEPARATOR, ltrim($vendorPath, DIRECTORY_SEPARATOR));

        if (!StorageUtil::isSubDirectoryOf($vendor, $repo)) {
            return $vendorPath . '/bin/' . $binary;
        }

        return $binary;
    }
}
