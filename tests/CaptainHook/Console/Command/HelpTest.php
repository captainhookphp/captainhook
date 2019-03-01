<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Console\Application\Setup;
use CaptainHook\App\Console\IO\DefaultIO;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
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

        $this->assertStringContainsString('help', $logs);
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

        $this->assertStringContainsString('-e', $logs);
        $this->assertStringContainsString('-f', $logs);
        $this->assertStringContainsString('-c', $logs);
    }
}
