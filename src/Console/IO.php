<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console;

/**
 * Interface IO
 *
 * @package HookMeUp
 * @author  Nils Adermann <naderman@naderman.de>
 * @author  Jordi Boggiano <j.boggiano@seld.be>
 * @author  Sebastian Feldmann <sf@sebastian.feldmann.info>
 * @link    https://github.com/sebastianfeldmann/hookmeup
 * @since   Class available since Release 0.9.0
 */
interface IO
{
    const QUIET = 1;
    const NORMAL = 2;
    const VERBOSE = 4;
    const VERY_VERBOSE = 8;
    const DEBUG = 16;

    /**
     * Is this input interactive?
     *
     * @return bool
     */
    public function isInteractive();

    /**
     * Is this output verbose?
     *
     * @return bool
     */
    public function isVerbose();

    /**
     * Is the output very verbose?
     *
     * @return bool
     */
    public function isVeryVerbose();

    /**
     * Is the output in debug verbosity?
     *
     * @return bool
     */
    public function isDebug();

    /**
     * Is this output decorated?
     *
     * @return bool
     */
    public function isDecorated();

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages  The message as an array of lines or a single string
     * @param bool         $newline   Whether to add a newline or not
     * @param int          $verbosity Verbosity level from the VERBOSITY_* constants
     */
    public function write($messages, $newline = true, $verbosity = self::NORMAL);

    /**
     * Writes a message to the error output.
     *
     * @param string|array $messages  The message as an array of lines or a single string
     * @param bool         $newline   Whether to add a newline or not
     * @param int          $verbosity Verbosity level from the VERBOSITY_* constants
     */
    public function writeError($messages, $newline = true, $verbosity = self::NORMAL);

    /**
     * Asks a question to the user.
     *
     * @param string|array $question The question to ask
     * @param string       $default  The default answer if none is given by the user
     *
     * @throws \RuntimeException If there is no data to read in the input stream
     * @return string            The user answer
     */
    public function ask($question, $default = null);

    /**
     * Asks a confirmation to the user.
     *
     * The question will be asked until the user answers by nothing, yes, or no.
     *
     * @param string|array $question The question to ask
     * @param bool         $default  The default answer if the user enters nothing
     *
     * @return bool true if the user has confirmed, false otherwise
     */
    public function askConfirmation($question, $default = true);

    /**
     * Asks for a value and validates the response.
     *
     * The validator receives the data to validate. It must return the
     * validated data when the data is valid and throw an exception
     * otherwise.
     *
     * @param string|array $question  The question to ask
     * @param callback     $validator A PHP callback
     * @param null|int     $attempts  Max number of times to ask before giving up (default of null means infinite)
     * @param mixed        $default   The default answer if none is given by the user
     *
     * @throws \Exception When any of the validators return an error
     * @return mixed
     */
    public function askAndValidate($question, $validator, $attempts = null, $default = null);

    /**
     * Asks the user to select a value.
     *
     * @param string|array $question     The question to ask
     * @param array        $choices      List of choices to pick from
     * @param bool|string  $default      The default answer if the user enters nothing
     * @param bool|int     $attempts     Max number of times to ask before giving up (false by default, which means infinite)
     * @param string       $errorMessage Message which will be shown if invalid value from choice list would be picked
     * @param bool         $multiSelect  Select more than one value separated by comma
     *
     * @throws \InvalidArgumentException
     * @return int|string|array          The selected value or values (the key of the choices array)
     */
    public function select($question, $choices, $default, $attempts = false, $errorMessage = 'Value "%s" is invalid', $multiSelect = false);
}
