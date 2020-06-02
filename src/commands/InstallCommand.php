<?php
namespace extas\commands;

use extas\components\Item;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\components\Plugins;
use extas\interfaces\crawlers\ICrawler;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\installers\IInstallerOption;
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
    const GENERATED_DATA__STORE = '.extas.install';
    const DEFAULT__PACKAGE_NAME = 'extas.json';

    protected const OPTION__APPLICATION_NAME = 'application';
    protected const OPTION__PACKAGE_NAME = 'package';
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
                static::OPTION__PACKAGE_NAME,
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

        $this->configureByPlugins();
    }

    /**
     * @return $this
     * @throws
     */
    protected function configureByPlugins()
    {
        $reservedOptions = [
            static::OPTION__PACKAGE_NAME => true,
            static::OPTION__REWRITE_GENERATED_DATA => true
        ];

        /**
         * @var $options IInstallerOption[]
         */
        $repo = new InstallerOptionRepository();
        $options = $repo->all([]);

        foreach ($options as $option) {
            if (isset($reservedOptions[$option->getName()])) {
                continue;
            }
            $this->addOption(...$option->__toInputOption());
        }

        return $this;
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
        $packageName = $input->getOption(static::OPTION__PACKAGE_NAME);
        $appName = $input->getOption(static::OPTION__APPLICATION_NAME);

        $output->writeln(['Searching packages...']);

        /**
         * @var ICrawler[] $crawlers
         */
        $crawlers = $this->getExtasApplication()->crawlerRepository()->all([]);

        $packages = [];
        foreach ($crawlers as $crawler) {
            $crawler->setParametersValues(['package_name' => $packageName]);
            $packages = array_merge($packages, $crawler->dispatch(getcwd(), $input, $output));
        }

        $output->writeln([
            'Found ' . count($packages) . ' packages.',
            'Installing application ' . $appName . ' with found packages...'
        ]);

        $generatedData = $this->runInstallStage($input, $output, $packages, IStageInstall::NAME . '.' . $appName);
        $generatedData = array_merge(
            $generatedData,
            $this->runInstallStage($input, $output, $packages)
        );

        $this->storeGeneratedData($generatedData, $input, $output);
    }

    /**
     * @return IItem
     */
    protected function getExtasApplication(): IItem
    {
        return new class extends Item {
            protected function getSubjectForExtension(): string
            {
                return 'extas.application';
            }
        };
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $packages
     * @param string $stage
     * @return array
     */
    protected function runInstallStage(
        InputInterface $input,
        OutputInterface $output,
        array $packages,
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
