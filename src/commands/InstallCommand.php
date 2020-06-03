<?php
namespace extas\commands;

use extas\components\options\TConfigure;
use extas\components\packages\TPrepareCommand;
use extas\components\Plugins;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageInstall;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @method crawlerRepository()
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class InstallCommand extends DefaultCommand
{
    use TConfigure;
    use TPrepareCommand;

    const GENERATED_DATA__STORE = '.extas.install';
    const DEFAULT__PACKAGE_NAME = 'extas.json';

    protected const OPTION__APPLICATION_NAME = 'application';
    protected const OPTION__PACKAGE_FILENAME = 'package_filename';
    protected const OPTION__REWRITE_GENERATED_DATA = 'rewrite';

    protected string $commandTitle = 'Extas installer';
    protected string $commandVersion = '3.0.0';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('install')
            ->setAliases(['i'])
            ->setDescription('Install entities using extas-compatible package file.')
            ->setHelp('This command allows you to install entities using extas-compatible package file.')
            ->addOption(
                static::OPTION__PACKAGE_FILENAME,
                'p',
                InputOption::VALUE_OPTIONAL,
                'Extas-compatible package name',
                static::DEFAULT__PACKAGE_NAME
            )->addOption(
                static::OPTION__APPLICATION_NAME,
                'a',
                InputOption::VALUE_OPTIONAL,
                'Current application name',
                'extas'
            )->addOption(
                static::OPTION__REWRITE_GENERATED_DATA,
                'r',
                InputOption::VALUE_OPTIONAL,
                'Rewrite generated data file',
                false
            )
        ;

        $this->configureWithOptions('extas-install', [
            static::OPTION__PACKAGE_FILENAME => true,
            static::OPTION__REWRITE_GENERATED_DATA => true,
            static::OPTION__APPLICATION_NAME => true
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $packages = $this->prepareCommand($input, $output, 'Installing');
        $appName = $input->getOption(static::OPTION__APPLICATION_NAME);
        $stage = IStageInstall::NAME . '.' . $appName;
        $generatedData = $this->runStage($input, $output, $packages, $stage);
        $generatedData = array_merge(
            $generatedData,
            $this->runStage($input, $output, $packages)
        );

        $this->storeGeneratedData($generatedData, $input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $packages
     * @param string $stage
     * @return array
     */
    protected function runStage(
        InputInterface $input,
        OutputInterface $output,
        array &$packages,
        string $stage = IStageInstall::NAME
    ): array
    {
        $generatedData = [];
        $pluginConfig = [
            IInstaller::FIELD__INPUT => $input,
            IInstaller::FIELD__OUTPUT => $output
        ];

        foreach (Plugins::byStage($stage, $this, $pluginConfig) as $plugin) {
            /**
             * @var IStageInstall $plugin
             */
            $plugin($packages, $generatedData);
        }

        return $generatedData;
    }

    /**
     * @param $data
     * @param $input InputInterface
     * @param $output OutputInterface
     */
    protected function storeGeneratedData($data, $input, $output)
    {
        if (!empty($data)) {
            $result = date('#[Y-m-d H:i:s]') . PHP_EOL;
            foreach ($data as $field => $value) {
                $result .= $field . ' = ' . $value . PHP_EOL;
            }
            $input->getOption(static::OPTION__REWRITE_GENERATED_DATA)
                ? file_put_contents(getcwd() . '/' . static::GENERATED_DATA__STORE, $result)
                : file_put_contents(getcwd() . '/' . static::GENERATED_DATA__STORE, $result, FILE_APPEND);

            $output->writeln(['<info>See generated data in the ' . static::GENERATED_DATA__STORE . '</info>']);
        }
    }
}
