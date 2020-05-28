<?php
namespace extas\commands;

use extas\components\packages\Crawler;
use extas\components\packages\Initializer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InitCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
class InitCommand extends DefaultCommand
{
    protected string $commandVersion = '0.1.0';
    protected string $commandTitle = 'Extas initializer';

    protected const OPTION__CONTAINER_REWRITE = 'container-rewrite';

    /**
     * Configure the current command.
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setAliases([])
            ->setDescription('Initialize environment for Extas. Run this command before extas install.')
            ->setHelp('This command allows you prepare all necessary files and other data. Uses extas.json .')
            ->addOption(
                static::OPTION__CONTAINER_REWRITE,
                'r',
                InputOption::VALUE_OPTIONAL,
                'Rewrite class-container file',
                true
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function dispatch(InputInterface $input, OutputInterface &$output): void
    {
        $this->prepareContainer($input, $output);
        $serviceCrawler = new Crawler();
        $configs = $serviceCrawler->crawlPackages(getcwd());
        $initializer = new Initializer();
        $initializer->run($configs, $output);
    }

    protected function prepareContainer(InputInterface $input, OutputInterface &$output)
    {
        $containerRewrite = $input->getOption(static::OPTION__CONTAINER_REWRITE);

        $lockContainerPath = getenv('EXTAS__CONTAINER_PATH_STORAGE_LOCK')
            ?: getcwd() . '/src/configs/container.php';

        $storageContainerPath = getenv('EXTAS__CONTAINER_PATH_STORAGE')
            ?: getcwd() . '/src/configs/container.json';

        if (!is_file($lockContainerPath) || $containerRewrite) {
            copy(
                getcwd() . '/vendor/jeyroik/extas-foundation/resources/container.dist.php',
                $lockContainerPath
            );
            $output->writeln([
                '<info>Copied container lock file.</info>'
            ]);
        } else {
            $output->writeln([
                '<comment>Container lock file already exists and rewrite is restricted.</comment>',
                'Tip: Use <comment>-r</comment> option to allow rewrite.'
            ]);
        }

        if (!is_file($storageContainerPath) || $containerRewrite) {
            copy(
                getcwd() . '/vendor/jeyroik/extas-foundation/resources/container.dist.json',
                $storageContainerPath
            );
            $output->writeln([
                '<info>Copied container storage file.</info>'
            ]);
        } else {
            $output->writeln([
                '<comment>Container storage file already exists and rewrite is restricted.</comment>',
                'Tip: Use <comment>-r</comment> option to allow rewrite.'
            ]);
        }
    }
}
