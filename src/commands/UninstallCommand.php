<?php
namespace extas\commands;

use extas\components\packages\Installer;
use extas\components\packages\CrawlerExtas;
use extas\components\packages\UnInstaller;
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
    public const OPTION__PACKAGE = 'package-name';
    public const OPTION__ENTITY = 'entity';


    const ARGUMENT__PATH = 'path';

    const OPTION__ALL = 'all';
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
            ->setAliases(['u'])

            // the short description shown while running "php bin/console list"
            ->setDescription('Uninstall extas packages and/or entities')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to uninstall entities by extas-compatible package file.')
            ->addOption(
                static::OPTION__PACKAGE,
                'p',
                InputOption::VALUE_OPTIONAL,
                'Package name for uninstall. Leave blank to uninstall all packages. ' .
                'Example: "extas/installer"',
                ''
            )
            ->addOption(
                static::OPTION__ENTITY,
                'e',
                InputOption::VALUE_OPTIONAL,
                'Entity name for uninstall. Leave blank to delete all entities. Example: plugins',
                ''
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $unInstaller = new UnInstaller([
            UnInstaller::FIELD__PACKAGE => $input->getOption(static::OPTION__PACKAGE),
            UnInstaller::FIELD__ENTITY => $input->getOption(static::OPTION__ENTITY),
            UnInstaller::FIELD__INPUT => $input,
            UnInstaller::FIELD__OUTPUT => $output
        ]);
        $unInstaller->uninstall();
    }
}
