<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Storage;

/**
 * Util class
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 1.0.4
 */
class Util
{
    /**
     * Array representation of a path.
     *
     * @param  string $path
     * @return array
     */
    public static function pathToArray(string $path) : array
    {
        return explode(DIRECTORY_SEPARATOR, ltrim($path, DIRECTORY_SEPARATOR));
    }

    /**
     * Is the given subDir a sub directory of given parentDir.
     *
     * @param  array $subDir
     * @param  array $parentDir
     * @return bool
     */
    public static function isSubDirectoryOf(array $subDir, array $parentDir) : bool
    {
        foreach ($parentDir as $index => $dir) {
            if (!isset($subDir[$index]) || $dir !== $subDir[$index]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return the relative path from parentDir to subDir.
     *
     * @param  array $subDir
     * @param  array $parentDir
     * @return string
     */
    public static function getSubPathOf(array $subDir, array $parentDir) : string
    {
        if (!self::isSubDirectoryOf($subDir, $parentDir)) {
            throw new \RuntimeException('Invalid sub directory');
        }

        $path = [];
        foreach (array_slice($subDir, count($parentDir)) as $dir) {
            $path[] = $dir;
        }
        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Return the path to the target path from inside the .git/hooks directory f.e. __DIR__ ../../vendor
     *
     * @param  string $repoDir
     * @param  string $targetPath
     * @return string
     * @throws \RuntimeException
     */
    public static function getTplTargetPath(string $repoDir, string $targetPath) : string
    {
        $repo   = explode(DIRECTORY_SEPARATOR, ltrim($repoDir, DIRECTORY_SEPARATOR));
        $target = explode(DIRECTORY_SEPARATOR, ltrim($targetPath, DIRECTORY_SEPARATOR));

        if (!self::isSubDirectoryOf($target, $repo)) {
            return '\'' . $targetPath;
        }

        return '__DIR__ . \'/../../' . self::getSubPathOf($target, $repo);
    }

    /**
     * Returns the path to the captainhook-run binary
     *
     * @param  string $repoDir
     * @param  string $vendorPath
     * @param  string $binary
     * @return string
     */
    public static function getBinaryPath(string $repoDir, string $vendorPath, string $binary): string
    {
        $repo   = explode(DIRECTORY_SEPARATOR, ltrim($repoDir, DIRECTORY_SEPARATOR));
        $vendor = explode(DIRECTORY_SEPARATOR, ltrim($vendorPath, DIRECTORY_SEPARATOR));

        if (!self::isSubDirectoryOf($vendor, $repo)) {
            return $vendorPath . '/bin/' . $binary;
        }

        return $binary;
    }
}
