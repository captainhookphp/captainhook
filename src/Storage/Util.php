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
abstract class Util
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
     * Convert array to path
     *
     * @param  array $path
     * @param  bool  $absolute
     * @return string
     */
    public static function arrayToPath(array $path, bool $absolute = false) : string
    {
        return ( $absolute ? DIRECTORY_SEPARATOR : '' ) . implode(DIRECTORY_SEPARATOR, $path);
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
     * @return array
     */
    public static function getSubPathOf(array $subDir, array $parentDir) : array
    {
        if (!self::isSubDirectoryOf($subDir, $parentDir)) {
            throw new \RuntimeException(
                'Invalid sub directory: '
                . implode('/', $subDir)
                . ' is not a sub directory of '
                . implode('/', $parentDir)
            );
        }

        $path = [];
        foreach (array_slice($subDir, count($parentDir)) as $dir) {
            $path[] = $dir;
        }
        return $path;
    }

    /**
     * Transforms an absolute path to a relative one
     *
     * @param  string $subPath     Absolute path to sub directory
     * @param  string $parentPath  Absolute path to parent directory
     * @return string
     */
    public static function getRelativePath(string $subPath, string $parentPath): string
    {
        return Util::arrayToPath(
            Util::getSubPathOf(
                Util::pathToArray($subPath),
                Util::pathToArray($parentPath)
            )
        );
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
        $repo   = self::pathToArray($repoDir);
        $target = self::pathToArray($targetPath);

        if (!self::isSubDirectoryOf($target, $repo)) {
            return '\'/' . implode('/', $target);
        }

        return '__DIR__ . \'/../../' . implode('/', self::getSubPathOf($target, $repo));
    }
}
