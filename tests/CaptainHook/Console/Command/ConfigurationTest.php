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

use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests Configure::run
     */
    public function testExecute()
    {
        $config    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(mt_rand(0, 9999)) . '.json';
        $configure = new Configuration();
        $output    = new DummyOutput();
        $input     = new ArrayInput(['--configuration' => $config]);

        $configure->setIO(new NullIO());
        $configure->run($input, $output);

        $this->assertTrue(file_exists($config));

        unlink($config);
    }
}
