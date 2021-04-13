<?php

class InstallCest
{
    public function gitHooksSuccessfullyInstallWithComposerUpdate(AcceptanceTester $I)
    {
        $repoPath = $I->initializeEmptyGitRepository();
        $I->mirrorDir(CH_PATH_FILES . '/template-acceptance', $repoPath);
        $I->addLocalCaptainHookToComposer($repoPath, CH_PATH_ROOT);
        $I->amInPath($repoPath);
        $I->runShellCommand('composer update --no-ansi --no-interaction');

        $I->seeInShellOutput("'commit-msg' hook installed successfully");
        $I->seeInShellOutput("'pre-push' hook installed successfully");
        $I->seeInShellOutput("'pre-commit' hook installed successfully");
        $I->seeInShellOutput("'prepare-commit-msg' hook installed successfully");
        $I->seeInShellOutput("'post-commit' hook installed successfully");
        $I->seeInShellOutput("'post-merge' hook installed successfully");
        $I->seeInShellOutput("'post-checkout' hook installed successfully");
        $I->seeInShellOutput("'post-rewrite' hook installed successfully");
        $I->seeInShellOutput("'post-change' hook installed successfully");
    }
}
