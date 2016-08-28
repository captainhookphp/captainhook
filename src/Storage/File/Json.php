<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Storage\File;

use HookMeUp\Storage\File;

/**
 * Class Json
 *
 * @package HookMeUp
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
class Json extends File
{
    /**
     * Read and decode the json file.
     *
     * @param  bool $assoc
     * @return \stdClass|array
     */
    public function read($assoc = false)
    {
        return json_decode(parent::read(), $assoc);
    }

    /**
     * Read the file and decode to assoc array.
     *
     * @return array
     */
    public function readAssoc()
    {
        return $this->read(true);
    }

    /**
     * Encode content to json and write to disk.
     *
     * @param mixed $content
     * @param int   $options
     */
    public function write($content, $options = 448)
    {
        $json = json_encode($content, $options) . ($options & JSON_PRETTY_PRINT ? "\n" : '');
        parent::write($json);
    }
}
