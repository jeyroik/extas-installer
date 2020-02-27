<?php
namespace extas\commands;

use extas\components\packages\Installer;
use extas\components\packages\Crawler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UninstallCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class UninstallCommand extends DefaultCommand
{
    const ARGUMENT__PATH = 'path';
    const OPTION__MASK = 'mask';

    protected string $commandVersion = '1.0.0';
    protected string $commandTitle = 'Extas uninstaller';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('uninstall')

            // the short description shown while running "php bin/console list"
            ->setDescription('Uninstall entities by extas-compatible package file')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to uninstall entities by extas-compatible package file.')
            ->addArgument(
                static::ARGUMENT__PATH,
                InputArgument::REQUIRED,
                'Path to search packages'
            )
            ->addOption(
                static::OPTION__MASK,
                'm',
                InputOption::VALUE_OPTIONAL,
                'Mask for deleting (ex. plugins)',
                '*'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $serviceCrawler = new Crawler();
        $configs = $serviceCrawler->crawlPackages($input->getArgument(static::ARGUMENT__PATH));

        $serviceInstaller = new Installer(['mask' => $input->getOption(static::OPTION__MASK)]);
        $serviceInstaller->uninstallMany($configs, $output);
    }
}
