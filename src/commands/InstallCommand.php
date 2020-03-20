<?php
namespace extas\commands;

use extas\components\packages\Crawler;
use extas\components\packages\Installer;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\interfaces\packages\installers\IInstallerOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class InstallCommand extends DefaultCommand
{
    const GENERATED_DATA__STORE = '.extas.install';
    const DEFAULT__PACKAGE_NAME = 'extas.json';

    protected const OPTION__PACKAGE_NAME = 'package';
    protected const OPTION__REWRITE_GENERATED_DATA = 'rewrite';
    protected const OPTION__REWRITE_CONTAINER = 'rewrite-container';
    protected const OPTION__REWRITE_ENTITY_ALLOW = 'rewrite-entity-allow';

    protected string $commandTitle = 'Extas installer';
    protected string $commandVersion = '1.0.0';

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
                static::OPTION__REWRITE_GENERATED_DATA,
                'r',
                InputOption::VALUE_OPTIONAL,
                'Rewrite generated data file',
                false
            )->addOption(
                static::OPTION__REWRITE_CONTAINER,
                'c',
                InputOption::VALUE_OPTIONAL,
                'Rewrite class-container file',
                true
            )->addOption(
                static::OPTION__REWRITE_ENTITY_ALLOW,
                'e',
                InputOption::VALUE_OPTIONAL,
                'Allow rewrite entity if it exists',
                true
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
            static::OPTION__REWRITE_GENERATED_DATA => true,
            static::OPTION__REWRITE_CONTAINER => true,
            static::OPTION__REWRITE_ENTITY_ALLOW => true,
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
        $rewriteContainer = $input->getOption(static::OPTION__REWRITE_CONTAINER);
        $rewriteAllow = $input->getOption(static::OPTION__REWRITE_ENTITY_ALLOW);

        $serviceCrawler = new Crawler([
            Crawler::FIELD__SETTINGS => [
                Crawler::SETTING__REWRITE_ALLOW => $rewriteAllow
            ],
        ]);
        $configs = $serviceCrawler->crawlPackages(getcwd(), $packageName);

        $serviceInstaller = new Installer([
            Installer::FIELD__REWRITE => $rewriteContainer,
            Installer::FIELD__INPUT => $input,
            Installer::FIELD__OUTPUT => $output
        ]);
        $serviceInstaller->installMany($configs);
        $this->storeGeneratedData($serviceInstaller->getGeneratedData(), $input, $output);
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

            $output->writeln([
                '<info>See generated data in the ' . static::GENERATED_DATA__STORE . '</info>'
            ]);
        }
    }
}
