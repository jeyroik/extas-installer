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
class ExportCommand extends Command
{
    const OPTION__SILENT = 'silent';
    const DEFAULT__PACKAGE_NAME = 'extas__exported.json';

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
        $start = microtime(true);
        $isSilent = $input->getOption(static::OPTION__SILENT);

        if ($isSilent) {
            $output = new NullOutput();
        }

        $output->writeln([
            'Extas exporter v1.0',
            '=========================='
        ]);

        $exporter = new Exporter([Exporter::FIELD__PATH => getcwd()]);
        $exporter->exportTo(static::DEFAULT__PACKAGE_NAME, [], $output);

        $end = microtime(true) - $start;
        $output->writeln([
            'See ' . getcwd() . '/' . static::DEFAULT__PACKAGE_NAME,
            '<info>Finished in ' . $end . ' s.</info>'
        ]);

        return 0;
    }
}
