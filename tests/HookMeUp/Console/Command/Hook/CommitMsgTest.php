<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\App\Console\Command\Hook;

use HookMeUp\App\Console\IO\NullIO;
use HookMeUp\App\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class CommitMsgTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CommitMsg::run
     */
    public function testExecute()
    {
        $repo = new DummyRepo();
        $repo->setup();

        $cmd    = new CommitMsg(HMU_PATH_FILES . '/config/valid.json', $repo->getPath());
        $output = new DummyOutput();
        $input  = new ArrayInput(
            [
                'file' => HMU_PATH_FILES . '/git/message/valid.txt'
            ]
        );

        $cmd->setIO(new NullIO());
        $cmd->run($input, $output);

        $repo->cleanup();
    }
}
