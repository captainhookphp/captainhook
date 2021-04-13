<?php

class PreCommitCest
{
    public const REPO_TEMPLATE = CH_PATH_FILES . '/template-acceptance';

    public function stashWorkingTreeChangesBeforePreCommit(AcceptanceTester $I)
    {
        $repositoryPath = $I->initializeEmptyGitRepository();

        $I->mirrorDir(self::REPO_TEMPLATE, $repositoryPath);
        $I->addLocalCaptainHookToComposer($repositoryPath, CH_PATH_ROOT);
        $I->amInPath($repositoryPath);
        $I->runShellCommand('composer update --no-ansi --no-interaction');
        $I->runShellCommand('git add .');
        $I->runShellCommand('git commit -m \'My initial commit\'');

        $I->enableHook($repositoryPath, 'pre-commit', [
            [
                'action' => 'echo "this is an action"',
            ],
            [
                'action' => 'git status --porcelain=v1',
            ],
        ]);

        // Add a file to the index and make changes to a file in the working tree.
        $I->touchFile('foo.txt');
        $I->runShellCommand('git add foo.txt');
        $I->appendToFile('README.md', "\nWorking tree changes that aren't staged.\n");

        // Look at `git status` to see the changes.
        $I->runShellCommand('git status --porcelain=v1');
        $I->seeInShellOutput(' M README.md');
        $I->seeInShellOutput('A  foo.txt');

        // Commit the file that's staged in the index.
        $I->runShellCommand('git commit -m \'Add foo.txt\'');

        // Output from actions appears in STDERR, so let's check it instead of STDOUT.
        // One of our actions is `git status`, so we want to assert that we do
        // not see the working tree changes listed, since they should have been
        // cached and cleared from the working tree.
        $I->seeInShellErr('this is an action');
        $I->cantSeeInShellErr(' M README.md');

        // Look at `git status` again for the things we expect to see (or not).
        $I->runShellCommand('git status --porcelain=v1');
        $I->seeInShellOutput(' M README.md');
        $I->cantSeeInShellOutput('A  foo.txt');
    }
}
