<?php
namespace extas\commands;

use extas\components\packages\Crawler;
use extas\components\packages\Installer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class InstallCommand extends Command
{
    const OPTION__SILENT = 'silent';
    const OPTION__PACKAGE_NAME = 'package';
    const OPTION__REWRITE_GENERATED_DATA = 'rewrite';
    const OPTION__REWRITE_CONTAINER = 'rewrite-container';
    const OPTION__FLUSH = 'flush';

    const GENERATED_DATA__STORE = '.extas.install';
    const DEFAULT__PACKAGE_NAME = 'extas.json';

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
                static::OPTION__SILENT,
                's',
                InputOption::VALUE_OPTIONAL,
                'Do not show output',
                true
            )
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
                static::OPTION__FLUSH,
                'f',
                InputOption::VALUE_OPTIONAL,
                'Flush data before install',
                ''
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
        $packageName = $input->getOption(static::OPTION__PACKAGE_NAME);
        $rewriteContainer = $input->getOption(static::OPTION__REWRITE_CONTAINER);
        $flush = $input->getOption(static::OPTION__FLUSH);

        if ($isSilent) {
            $output = new NullOutput();
        }

        $output->writeln([
            'Extas installer v1.2',
            '=========================='
        ]);

        $this->prepareClassContainer($rewriteContainer);

        $serviceCrawler = new Crawler();
        $configs = $serviceCrawler->crawlPackages(getcwd(), $packageName);

        $serviceInstaller = new Installer([
            Installer::FIELD__REWRITE => $rewriteContainer,
            Installer::FIELD__FLUSH => $flush
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
