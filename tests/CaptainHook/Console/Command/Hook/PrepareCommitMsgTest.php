<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Console\Command\Hook;

use CaptainHook\App\Console\Runtime\Resolver;
use CaptainHook\App\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

class PrepareCommitMsgTest extends TestCase
{
    /**
     * Tests PrepareCommitMsg::run
     *
     * @throws \Exception
     */
    public function testExecute(): void
    {
        $repo   = new DummyRepo();
        $output = new NullOutput();
        $input  = new ArrayInput(
            [
                '--configuration' => CH_PATH_FILES . '/config/valid.json',
                '--git-directory' => $repo->getGitDir(),
                'file'            => CH_PATH_FILES . '/git/message/valid.txt',
                'mode'            => 'message'
            ]
        );

        $cmd = new PrepareCommitMsg(new Resolver());
        $cmd->run($input, $output);

        $this->assertTrue(true);
    }
}
