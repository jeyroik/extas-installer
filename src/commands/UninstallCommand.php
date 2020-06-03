<?php
namespace extas\commands;

use extas\components\options\TConfigure;
use extas\components\packages\TPrepareCommand;
use extas\components\Plugins;
use extas\interfaces\stages\IStageUninstall;
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
    use TConfigure;
    use TPrepareCommand;

    protected const OPTION__PACKAGE = 'package';
    protected const OPTION__SECTION = 'section';
    protected const OPTION__APPLICATION_NAME = 'application';

    protected string $commandVersion = '1.0.0';
    protected string $commandTitle = 'Extas uninstaller';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            ->setName('uninstall')
            ->setAliases(['u'])
            ->setDescription('Uninstall extas packages and/or entities')
            ->setHelp('extas u -p "extas/installer"')
            ->addOption(
                static::OPTION__APPLICATION_NAME,
                'a',
                InputOption::VALUE_OPTIONAL,
                'Application name for uninstall.',
                'extas'
            )->addOption(
                static::OPTION__PACKAGE,
                'p',
                InputOption::VALUE_OPTIONAL,
                'Package name for uninstall. Leave blank to uninstall all packages. ' .
                'Example: "extas/installer"',
                ''
            )
            ->addOption(
                static::OPTION__SECTION,
                's',
                InputOption::VALUE_OPTIONAL,
                'Section name for uninstall. Leave blank to delete all entities. Example: plugins',
                ''
            )
        ;

        $this->configureWithOptions('extas-uninstall', [
            static::OPTION__PACKAGE => true,
            static::OPTION__SECTION => true,
            static::OPTION__APPLICATION_NAME => true
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $appName = $input->getOption(static::OPTION__APPLICATION_NAME);
        $packages = $this->prepareCommand($input, $output, 'Uninstalling');

        $this->runStage($input, $output, $packages, IStageUninstall::NAME . '.' . $appName);
        $this->runStage($input, $output, $packages);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $packages
     * @param string $stage
     * @return $this
     */
    protected function runStage(
        InputInterface $input,
        OutputInterface $output,
        array &$packages,
        string $stage = IStageUninstall::NAME
    )
    {
        $pluginConfig = [
            IStageUninstall::FIELD__INPUT => $input,
            IStageUninstall::FIELD__OUTPUT => $output
        ];

        foreach (Plugins::byStage($stage, $this, $pluginConfig) as $plugin) {
            /**
             * @var IStageUninstall $plugin
             */
            $plugin($packages);
        }

        return $this;
    }
}
