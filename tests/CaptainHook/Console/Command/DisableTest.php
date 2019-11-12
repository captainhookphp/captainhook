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

use CaptainHook\App\Console\IO\DefaultIO;
use CaptainHook\App\Console\IO\NullIO;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;
use PHPUnit\Framework\TestCase;

class DisableTest extends TestCase
{
    /**
     * Tests Enable::run
     */
    public function testExecuteNoConfig(): void
    {
        $this->expectException(\Exception::class);

        $input   = new ArrayInput(
            [
                'hook' => 'pre-commit',
                '--configuration' => 'foo'
            ]
        );
        $output  = new DummyOutput();
        $install = new Disable();
        $install->setIO(new NullIO());
        $install->run($input, $output);
    }

    /**
     * Tests Enable::run
     */
    public function testExecuteEnablePrePush(): void
    {
        $config = sys_get_temp_dir() . '/captainhook-enable.json';
        copy(CH_PATH_FILES . '/config/valid.json', $config);


        $add    = new Disable();
        $output = new DummyOutput();
        $input  = new ArrayInput(
            [
                'hook'            => 'pre-commit',
                '--configuration' => $config
            ]
        );

        $io = $this->getMockBuilder(DefaultIO::class)
                   ->disableOriginalConstructor()
                   ->getMock();
        $io->expects($this->once())->method('write');

        $add->setIO($io);
        $add->run($input, $output);

        $json = json_decode(file_get_contents($config), true);
        $this->assertFalse($json['pre-commit']['enabled']);

        unlink($config);
    }
}
