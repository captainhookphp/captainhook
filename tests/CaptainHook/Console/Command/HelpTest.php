<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Command;

use SebastianFeldmann\CaptainHook\Console\Application\Setup;
use SebastianFeldmann\CaptainHook\Console\IO\DefaultIO;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class HelpTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests Help::run
     */
    public function testExecuteNoCommand()
    {
        $input  = new ArrayInput([]);
        $output = new BufferedOutput();
        $io     = new DefaultIO($input, $output);
        $app    = new Setup();
        $help   = new Help();
        $help->setIO($io);
        $help->setApplication($app);
        $help->run($input, $output);

        $logs = $output->fetch();

        $this->assertTrue(strpos($logs, 'help') > 0);
    }

    /**
     * Tests Help::run
     */
    public function testExecuteWithCommand()
    {
        $input  = new ArrayInput([]);
        $output = new BufferedOutput();
        $io     = new DefaultIO($input, $output);
        $help   = new Help();
        $cmd    = New Configuration();

        $help->setCommand($cmd);
        $help->setIO($io);
        $help->run($input, $output);

        $logs = $output->fetch();

        $this->assertTrue(strpos($logs, '-e') > 0);
        $this->assertTrue(strpos($logs, '-f') > 0);
        $this->assertTrue(strpos($logs, '-c') > 0);
    }
}
