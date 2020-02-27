<?php
namespace extas\commands;

use extas\components\packages\Exporter;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExportCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class ExportCommand extends DefaultCommand
{
    const DEFAULT__PACKAGE_NAME = 'extas__exported.json';

    protected string $commandTitle = 'Extas exporter';
    protected string $commandVersion = '1.0.0';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('export')
            ->setAliases(['e'])

            // the short description shown while running "php bin/console list"
            ->setDescription('Export entities to the extas-compatible package file.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to export entities to the extas-compatible package file.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $exporter = new Exporter([Exporter::FIELD__PATH => getcwd()]);
        $exporter->exportTo(static::DEFAULT__PACKAGE_NAME, [], $output);

        $output->writeln([
            'See ' . getcwd() . '/' . static::DEFAULT__PACKAGE_NAME,
        ]);
    }
}
