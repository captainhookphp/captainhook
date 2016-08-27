<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Console\Command\Hook;

use HookMeUp\Console\IO\NullIO;
use HookMeUp\Git\DummyRepo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

class PreCommitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests CommitMsg::run
     */
    public function testExecute()
    {
        $repo = new DummyRepo();
        $repo->setup();

        $cmd    = new PreCommit(HMU_PATH_FILES . '/config/empty.json', $repo->getPath());
        $output = new DummyOutput();
        $input  = new ArrayInput([]);

        $cmd->setIO(new NullIO());
        $cmd->run($input, $output);

        $repo->cleanup();
    }
}
