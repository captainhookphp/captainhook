<?php

namespace Helper;

use Codeception\Module\Cli as CliModule;
use Codeception\PHPUnit\TestCase;
use PHPUnit\Framework\Assert;

/**
 * Cli Helper for CaptainHook acceptance tests.
 *
 * Don't forget! If you make changes to this, you must run:
 *
 *     php vendor/bin/codecept build
 *
 * Then, commit the changes to the generated actor classes (as well as this one).
 */
class Cli extends CliModule
{
    private const DESCRIPTOR_SPEC = [
        0 => ['pipe', 'r'], // stdin
        1 => ['pipe', 'w'], // stdout
        2 => ['pipe', 'w'], // stderr
    ];

    public $err = '';

    /**
     * {@inheritDoc}
     *
     * Overrides the parent to use `proc_open()` instead of `exec()`.
     *
     * @return void
     */
    public function runShellCommand($command, $failNonZero = true)
    {
        $pipes = [];

        $process = proc_open($command, self::DESCRIPTOR_SPEC, $pipes);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $this->result = proc_close($process);
        $this->output = $stdout;
        $this->err = $stderr;

        if ($this->output === false) {
            Assert::fail("Cannot execute command: {$command}");
        }

        if ($this->result !== 0 && $failNonZero) {
            Assert::fail(
                "Result code was {$this->result}.\n\n"
                . "STDOUT:\n{$this->output}"
                . "\n\n"
                . "STDERR:\n{$this->err}"
            );
        }

        $this->debug(preg_replace('~s/\e\[\d+(?>(;\d+)*)m//g~', '', $this->output));
        $this->debug(preg_replace('~s/\e\[\d+(?>(;\d+)*)m//g~', '', $this->err));
    }

    /**
     * Checks that stderr from last executed command contains text
     *
     * @param $text
     * @return void
     */
    public function seeInShellErr($text)
    {
        TestCase::assertStringContainsString($text, $this->err);
    }

    /**
     * Checks that stderr from latest command doesn't contain text
     *
     * @param $text
     * @return void
     *
     */
    public function dontSeeInShellErr($text)
    {
        $this->debug($this->err);
        TestCase::assertStringNotContainsString($text, $this->err);
    }

    /**
     * Checks that stderr from last executed command matches regex
     *
     * @param $regex
     * @return void
     */
    public function seeShellErrMatches($regex)
    {
        TestCase::assertRegExp($regex, $this->err);
    }

    /**
     * Returns the stderr from latest command
     */
    public function grabShellErr(): string
    {
        return $this->err;
    }
}
