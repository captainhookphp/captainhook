<?php
declare(strict_types=1);

namespace CaptainHook\App\Hook;

use CaptainHook\App\Config;
use CaptainHook\App\Hook\Template\Docker;
use CaptainHook\App\Hook\Template\Local;
use SebastianFeldmann\Git\Repository;
use Symfony\Component\Console\Input\InputInterface;

abstract class TemplateBuilder
{
    /**
     * @param InputInterface $input
     * @param Config         $config
     * @param Repository     $repository
     *
     * @return Template
     */
    public static function build(InputInterface $input, Config $config, Repository $repository): Template
    {
        if ($input->getOption('run-mode') === Template::DOCKER) {
            return new Docker(
                realpath($repository->getRoot()),
                getcwd() . '/vendor',
                $input->getOption('container-name')
            );
        }

        return new Local(
            realpath($repository->getRoot()),
            getcwd() . '/vendor',
            realpath($config->getPath())
        );
    }
}