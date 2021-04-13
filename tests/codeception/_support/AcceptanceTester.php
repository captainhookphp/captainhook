<?php

use Codeception\Actor;
use Codeception\PHPUnit\TestCase;
use Codeception\Scenario;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        $this->filesystem = new Filesystem();
    }

    /**
     * Creates the requested directory, recursively if needed.
     *
     * @param string $path
     * @return void
     */
    public function makeDir(string $path): void
    {
        try {
            $this->filesystem->mkdir($path);
        } catch (IOException $exception) {
            TestCase::fail($exception->getMessage());
        }
    }

    /**
     * Creates the requested file.
     *
     * @param string $path
     * @return void
     */
    public function touchFile(string $path): void
    {
        try {
            $this->filesystem->touch($path);
        } catch (IOException $exception) {
            TestCase::fail($exception->getMessage());
        }
    }

    public function appendToFile(string $path, string $contents): void
    {
        try {
            $this->filesystem->appendToFile($path, $contents);
        } catch (IOException $exception) {
            TestCase::fail($exception->getMessage());
        }
    }

    /**
     * Recursively copies files from $source to $destination.
     *
     * @param string $source A directory from which to copy files.
     * @param string $destination A directory to which to copy files.
     * @return void
     */
    public function mirrorDir(string $source, string $destination): void
    {
        try {
            $this->filesystem->mirror($source, $destination);
        } catch (IOException $exception) {
            TestCase::fail(
                "Failed to mirror directory {$source} to {$destination}; {$exception->getMessage()}"
            );
        }
    }

    /**
     * Creates and initializes a Git repository in the system's
     * temporary directory.
     *
     * @return string The path to the Git repository.
     */
    public function initializeEmptyGitRepository(): string
    {
        $repoPath = sys_get_temp_dir()
            . '/CaptainHook/tests/repo-'
            . time() . '-' . bin2hex(random_bytes(4));

        $gitConfigFile = $repoPath . '/.git/config';

        $this->makeDir($repoPath);

        $this->runShellCommand(
            'git init --initial-branch=main ' . escapeshellarg($repoPath)
        );
        $this->runShellCommand(
            'git config --file ' . escapeshellarg($gitConfigFile) . ' user.name "Acceptance Tester"'
        );
        $this->runShellCommand(
            'git config --file ' . escapeshellarg($gitConfigFile) . ' user.email "text@example.com"'
        );

        return $repoPath;
    }

    /**
     * Adds an entry to the "repositories" property in composer.json, pointing
     * to the local CaptainHook source code being tested.
     *
     * @param string $repositoryPath
     * @param string $localCaptainHookPath
     * @return void
     */
    public function addLocalCaptainHookToComposer(
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
    public function enableHook(
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
        $this->filesystem->dumpFile(
            $filename,
            json_encode(
                $contents,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            )
        );
    }
}
