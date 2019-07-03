<?php
namespace extas\commands;

use df\components\Crawler;
use df\components\Installer;

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
        $packageName = $input->getOption(static::OPTION__PACKAGE_NAME);

        if ($isSilent) {
            $output = new NullOutput();
        }

        $output->writeln([
            'Extas installer v1.1',
            '=========================='
        ]);

        $this->prepareClassContainer();

        $serviceCrawler = new Crawler();
        $dfConfigs = $serviceCrawler->crawlDfPackages(getcwd(), $packageName);

        $serviceInstaller = new Installer();
        $serviceInstaller->installMany($dfConfigs, $output);
        $this->storeGeneratedData($serviceInstaller->getGeneratedData(), $input, $output);

        $output->writeln(['<notice>Finished</notice>']);

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
     */
    protected function prepareClassContainer()
    {
        $extasContainerPath = getenv('EXTAS__CONTAINER_PATH') ?: getcwd() . '/configs/container.php';
        $dfContainerPath = getenv('DF__CONTAINER_PATH') ?: getcwd() . '/configs/container.json';

        copy(
            getcwd(). '/vendor/df/foundation/resources/container.dist.php',
            $extasContainerPath
        );

        copy(
            getcwd(). '/vendor/df/foundation/resources/container.json',
            $dfContainerPath
        );
    }
}
