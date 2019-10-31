<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Console\Command;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO;
use CaptainHook\App\Console\IOUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Base
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 0.9.0
 */
abstract class Base extends Command
{
    /**
     * Input output handler
     *
     * @var \CaptainHook\App\Console\IO
     */
    private $io;

    /**
     * CaptainHook configuration
     *
     * @var \CaptainHook\App\Config
     */
    private $config;

    /**
     * IO setter
     *
     * @param \CaptainHook\App\Console\IO $io
     */
    public function setIO(IO $io) : void
    {
        $this->io = $io;
    }

    /**
     * IO interface getter
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return \CaptainHook\App\Console\IO
     */
    public function getIO(InputInterface $input, OutputInterface $output) : IO
    {
        if (null === $this->io) {
            $this->io = new IO\DefaultIO($input, $output, $this->getHelperSet());
        }
        return $this->io;
    }

    /**
     * Return list of available options to overwrite the configuration settings
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  array                                           $settingNames
     * @return array
     */
    protected function fetchInputSettings(InputInterface $input, array $settingNames): array
    {
        $settings = [];
        foreach ($settingNames as $setting) {
            $value = IOUtil::argToString($input->getOption($setting));
            if (!empty($value)) {
                $settings[$setting] = $value;
            }
        }
        return $settings;
    }

    /**
     * CaptainHook config factory
     *
     * @param  string $path
     * @param  bool   $failIfNotFound
     * @param  array  $settings
     * @return \CaptainHook\App\Config
     * @throws \Exception
     */
    protected function getConfig(string $path = '', bool $failIfNotFound = false, array $settings = []) : Config
    {
        $this->config = Config\Factory::create($path, $settings);

        if ($failIfNotFound && !$this->config->isLoadedFromFile()) {
            throw new \RuntimeException(
                'Please create a captainhook configuration first' . PHP_EOL .
                'Run \'captainhook configure\'' . PHP_EOL .
                'If you have a configuration located elsewhere use the --configuration option'
            );
        }
        return $this->config;
    }
}
