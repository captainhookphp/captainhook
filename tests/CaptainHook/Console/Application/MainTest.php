<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\CaptainHook\Console\Application;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class MainTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $output = new NullOutput();
        $input  = new ArrayInput([
            'command'         => 'run',
            'hook'            => 'pre-push',
            '--configuration' => CH_PATH_FILES . DIRECTORY_SEPARATOR . 'config/valid.json'
        ]);
        $app = new Main();
        $app->setAutoExit(false);
        $app->run($input, $output);
    }
}
