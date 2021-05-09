<?php

namespace CaptainHook\App\Integration;

use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class IntegrationTestCase extends TestCase
{
    public const REPO_TEMPLATE = CH_PATH_FILES . '/template-acceptance';

    /**
     * @return Filesystem
     */
    protected function filesystem(): Filesystem
    {
        return new Filesystem();
    }

    /**
     * Sets up a repository to use for testing and returns its path.
     *
     * @param bool $runComposerUpdate Whether to run `composer update` in the repository.
     * @return string The path to the repository.
     */
    protected function setUpRepository(bool $runComposerUpdate = true): string
    {
        $repoPath = $this->initializeEmptyGitRepository();
        $this->filesystem()->mirror(self::REPO_TEMPLATE, $repoPath);
        $this->addLocalCaptainHookToComposer($repoPath, CH_PATH_ROOT);

        $this->mustRunInShell(['git', 'add', '.'], $repoPath);
        $this->mustRunInShell(['git', 'commit', '-m', 'My initial commit'], $repoPath);

        if ($runComposerUpdate === true) {
            $this->mustRunInShell(['composer', 'update', '--no-ansi', '--no-interaction'], $repoPath);
        }

        return $repoPath;
    }

    /**
     * Creates and initializes a Git repository in the system's
     * temporary directory.
     *
     * @return string The path to the Git repository.
     */
    protected function initializeEmptyGitRepository(): string
    {
        try {
            $repoPath = sys_get_temp_dir()
                . '/CaptainHook/tests/repo-'
                . time() . '-' . bin2hex(random_bytes(4));
        } catch (Exception $exception) {
            TestCase::fail($exception->getMessage());
        }

        $gitConfigFile = $repoPath . '/.git/config';

        $this->filesystem()->mkdir($repoPath);

        $this->mustRunInShell(['git', 'init', '--initial-branch=main', $repoPath]);
        $this->mustRunInShell(['git', 'config', '--file', $gitConfigFile, 'user.name', 'Acceptance Tester']);
        $this->mustRunInShell(['git', 'config', '--file', $gitConfigFile, 'user.email', 'test@example.com']);

        return $repoPath;
    }

    /**
     * Runs a shell command
     *
     * @param array $command The command to run and its arguments listed as separate entries
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     * @param array|null $env The environment variables or null to use the same environment as the current PHP process
     * @param mixed $input The input as stream resource, scalar or \Traversable, or null for no input
     * @param bool $throwOnFailure Throw exception if the process fails
     * @return ProcessResult
     */
    protected function runInShell(
        array $command,
        ?string $cwd = null,
        ?array $env = null,
        $input = null,
        bool $throwOnFailure = false
    ): ProcessResult {
        $process = new Process($command, $cwd, $env, $input);

        if ($throwOnFailure === true) {
            $process->mustRun();
        } else {
            $process->run();
        }

        return new ProcessResult($process->getExitCode(), $process->getOutput(), $process->getErrorOutput());
    }

    /**
     * Runs a shell command, throwing an exception if it fails.
     *
     * @param array $command The command to run and its arguments listed as separate entries
     * @param string|null $cwd The working directory or null to use the working dir of the current PHP process
     * @param array|null $env The environment variables or null to use the same environment as the current PHP process
     * @param mixed $input The input as stream resource, scalar or \Traversable, or null for no input
     * @return ProcessResult
     */
    protected function mustRunInShell(
        array $command,
        ?string $cwd = null,
        ?array $env = null,
        $input = null
    ): ProcessResult {
        return $this->runInShell($command, $cwd, $env, $input, true);
    }

    /**
     * Adds an entry to the "repositories" property in composer.json, pointing
     * to the local CaptainHook source code being tested.
     *
     * @param string $repositoryPath
     * @param string $localCaptainHookPath
     * @return void
     */
    protected function addLocalCaptainHookToComposer(
        string $repositoryPath,
        string $localCaptainHookPath
    ): void {
        $composerFile = $repositoryPath . '/composer.json';
        $composerContents = $this->getJson($composerFile);

        if (!isset($composerContents['repositories'])) {
            $composerContents['repositories'] = [];
        }

        $composerContents['repositories'][] = [
            'type' => 'path',
            'url' => $localCaptainHookPath,
            'options' => [
                'symlink' => true,
            ],
        ];

        $this->writeJson($composerFile, $composerContents);
    }

    /**
     * Enables a hook in captainhook.json and configures actions for it.
     *
     * @param string $repositoryPath
     * @param string $hookName
     * @param array $actions
     * @return void
     */
    protected function enableHook(
        string $repositoryPath,
        string $hookName,
        array $actions = []
    ): void {
        $captainHookFile = $repositoryPath . '/captainhook.json';
        $captainHookContents = $this->getJson($captainHookFile);

        $captainHookContents[$hookName] = [
            'enabled' => true,
            'actions' => $actions,
        ];

        $this->writeJson($captainHookFile, $captainHookContents);
    }

    /**
     * Sets a config value in captainhook.json
     *
     * @param string $repositoryPath
     * @param string $configName
     * @param mixed $value
     * @return void
     */
    protected function setConfig(string $repositoryPath, string $configName, $value): void
    {
        $captainHookFile = $repositoryPath . '/captainhook.json';
        $captainHookContents = $this->getJson($captainHookFile);

        $captainHookContents['config'][$configName] = $value;

        $this->writeJson($captainHookFile, $captainHookContents);
    }

    /**
     * Returns the parsed contents of a JSON file.
     *
     * @param string $filename
     * @return array
     */
    private function getJson(string $filename): array
    {
        TestCase::assertFileExists($filename);

        $json = json_decode(file_get_contents($filename), true);

        if ($json === null) {
            TestCase::fail(json_last_error_msg());
        }

        return $json;
    }

    /**
     * Encodes $contents as JSON and writes it to $filename.
     *
     * @param string $filename
     * @param array $contents
     * @return void
     */
    private function writeJson(string $filename, array $contents): void
    {
        $this->filesystem()->dumpFile(
            $filename,
            json_encode(
                $contents,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );
    }
}
