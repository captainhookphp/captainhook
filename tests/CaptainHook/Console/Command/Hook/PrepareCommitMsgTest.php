<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Command\Hook;

use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class PrepareCommitMsgTest extends TestCase
{
    /**
     * Tests PrepareCommitMsg::run
     */
    public function testExecute(): void
    {
        if (\defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $repo = new DummyRepo();
        $repo->setup();

        $cmd    = new PrepareCommitMsg(CH_PATH_FILES . '/config/valid.json', $repo->getPath());
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                'file' => CH_PATH_FILES . '/git/message/valid.txt',
                'mode' => 'message'
            ]
        );

        $cmd->setIO(new NullIO());
        $cmd->run($input, $output);

        $repo->cleanup();

        $this->assertTrue(true);
    }
}
