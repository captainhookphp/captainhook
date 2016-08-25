<?php
/**
 * This file is part of HookMeUp.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace HookMeUp\Hook\Message;

use HookMeUp\Config;
use HookMeUp\Console\IO\NullIO;
use HookMeUp\Git\CommitMessage;
use HookMeUp\Git\Repository;

class BeamsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Beams::execute
     */
    public function testExecute()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/hookmeup.json');
        $action = new Config\Action('php', '\\HookMeUp\\Hook\\Message\\Beams');
        $repo   = new Repository(HMU_PATH_FILES . '/git/repo-default');
        $repo->setCommitMsg(new CommitMessage('Foo bar baz'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Beams::execute
     *
     * @expectedException \Exception
     */
    public function testExecuteFail()
    {
        $io     = new NullIO();
        $config = new Config(HMU_PATH_FILES . '/hookmeup.json');
        $action = new Config\Action('php', '\\HookMeUp\\Hook\\Message\\Beams');
        $repo   = new Repository(HMU_PATH_FILES . '/git/repo-default');
        $repo->setCommitMsg(new CommitMessage('foo bar baz.'));

        $standard = new Beams();
        $standard->execute($config, $io, $repo, $action);
    }
}
