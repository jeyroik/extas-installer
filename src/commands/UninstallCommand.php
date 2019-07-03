<?php
namespace extas\commands;

use extas\components\packages\Installer;
use extas\components\packages\Crawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UninstallCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class UninstallCommand extends Command
{
    const ARGUMENT__PATH = 'path';
    const OPTION__SILENT = 'silent';
    const OPTION__MASK = 'mask';

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
            ->addOption(
                static::OPTION__SILENT,
                's',
                InputOption::VALUE_OPTIONAL,
                'Do not show output',
                true
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|mixed
     * @throws
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isSilent = $input->getOption(static::OPTION__SILENT);

        if ($isSilent) {
            $output = new NullOutput();
        }

        $output->writeln([
            'Extas uninstall tool v1.1',
            '=========================='
        ]);

        $serviceCrawler = new Crawler();
        $dfConfigs = $serviceCrawler->crawlPackages($input->getArgument(static::ARGUMENT__PATH));

        $serviceInstaller = new Installer(['mask' => $input->getOption(static::OPTION__MASK)]);
        $serviceInstaller->uninstallMany($dfConfigs, $output);

        $output->writeln([
            'Done'
        ]);

        return 0;
    }
}
