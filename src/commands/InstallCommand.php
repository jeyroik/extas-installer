<?php
namespace extas\commands;

use extas\components\packages\Crawler;
use extas\components\packages\Installer;

use extas\components\Plugins;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class InstallCommand extends Command
{
    const GENERATED_DATA__STORE = '.extas.install';
    const DEFAULT__PACKAGE_NAME = 'extas.json';

    protected const VERSION = '0.5.0';
    protected const OPTION__PACKAGE_NAME = 'package';
    protected const OPTION__REWRITE_GENERATED_DATA = 'rewrite';
    protected const OPTION__REWRITE_CONTAINER = 'rewrite-container';
    protected const OPTION__FLUSH = 'flush';
    protected const OPTION__REWRITE_ENTITY_ALLOW = 'rewrite-entity-allow';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('install')
            ->setAliases(['i'])

            // the short description shown while running "php bin/console list"
            ->setDescription('Install entities using extas-compatible package file.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to install entities using extas-compatible package file.')
            ->addOption(
                static::OPTION__PACKAGE_NAME,
                'p',
                InputOption::VALUE_OPTIONAL,
                'Extas-compatible package name',
                static::DEFAULT__PACKAGE_NAME
            )
            ->addOption(
                static::OPTION__REWRITE_GENERATED_DATA,
                'r',
                InputOption::VALUE_OPTIONAL,
                'Rewrite generated data file',
                false
            )
            ->addOption(
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
            )->addOption(
                static::OPTION__FLUSH,
                'f',
                InputOption::VALUE_OPTIONAL,
                'Flush data before install',
                ''
            )
        ;

        $this->configureByPlugins();
    }

    /**
     * @return $this
     */
    protected function configureByPlugins()
    {
        $addedOptions = [
            static::OPTION__PACKAGE_NAME => true,
            static::OPTION__REWRITE_GENERATED_DATA => true,
            static::OPTION__REWRITE_CONTAINER => true,
            static::OPTION__REWRITE_ENTITY_ALLOW => true,
            static::OPTION__FLUSH => true
        ];

        foreach (Plugins::byStage('extas.install.options') as $plugin) {
            $option = $plugin();

            if (!isset($option[0]) || isset($addedOptions[$option[0]])) {
                // incorrect option or already exists
                continue;
            }

            if (count($option) === 5) {
                $this->addOption(...$option);
            }
        }

        return $this;
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
        $packageName = $input->getOption(static::OPTION__PACKAGE_NAME);
        $rewriteContainer = $input->getOption(static::OPTION__REWRITE_CONTAINER);
        $flush = $input->getOption(static::OPTION__FLUSH);
        $rewriteAllow = $input->getOption(static::OPTION__REWRITE_ENTITY_ALLOW);

        $output->writeln([
            'Extas installer v' . static::VERSION,
            '=========================='
        ]);

        $this->prepareClassContainer($rewriteContainer);

        $serviceCrawler = new Crawler([
            Crawler::FIELD__SETTINGS => [
                Crawler::SETTING__REWRITE_ALLOW => $rewriteAllow
            ],
        ]);
        $configs = $serviceCrawler->crawlPackages(getcwd(), $packageName);

        $serviceInstaller = new Installer([
            Installer::FIELD__REWRITE => $rewriteContainer,
            Installer::FIELD__FLUSH => $flush,
            Installer::FIELD__INPUT => $input
        ]);
        $serviceInstaller->installMany($configs, $output);
        $this->storeGeneratedData($serviceInstaller->getGeneratedData(), $input, $output);

        $end = microtime(true) - $start;
        $output->writeln(['<info>Finished in ' . $end . ' s.</info>']);

        return 0;
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

    /**
     * Copy container base dist
     *
     * @param bool $rewriteContainer
     */
    protected function prepareClassContainer($rewriteContainer = true)
    {
        $lockContainerPath = getenv('EXTAS__CONTAINER_PATH_STORAGE_LOCK')
            ?: getcwd() . '/configs/container.php';

        $storageContainerPath = getenv('EXTAS__CONTAINER_PATH_STORAGE')
            ?: getcwd() . '/configs/container.json';

        if ($rewriteContainer || !is_file($lockContainerPath)) {
            copy(
                getcwd() . '/vendor/jeyroik/extas-foundation/resources/container.dist.php',
                $lockContainerPath
            );
        }

        if ($rewriteContainer || !is_file($storageContainerPath)) {
            copy(
                getcwd() . '/vendor/jeyroik/extas-foundation/resources/container.dist.json',
                $storageContainerPath
            );
        }
    }
}
