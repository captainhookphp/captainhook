<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Console\Runtime\Resolver;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * Tests Configure::run
     *
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $resolver  = new Resolver();
        $config    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5(mt_rand(0, 9999)) . '.json';
        $output    = new NullOutput();
        $input     = new ArrayInput(['--configuration' => $config]);

        $configure = new Configuration($resolver);
        $configure->setIO(new NullIO());
        $configure->run($input, $output);

        $this->assertFileExists($config);

        unlink($config);
    }
}
