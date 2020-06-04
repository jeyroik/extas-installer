<?php
namespace extas\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DefaultCommand
 *
 * @package extas\commands
 * @author jeyroik@gmail.com
 */
abstract class DefaultCommand extends Command
{
    protected string $commandTitle = 'command';
    protected string $commandVersion = '0.0.0';

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

        $output->writeln([
            $this->commandTitle . ' v' . $this->commandVersion,
            '=========================='
        ]);

        try {
            $this->dispatch($input, $output);
        } catch (\Exception $e) {
            $output->writeln(['<error>' . $e->getMessage() . '</error>']);
        }

        $end = microtime(true) - $start;
        $output->writeln(['<info>Finished in ' . $end . ' s.</info>']);

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    abstract protected function dispatch(InputInterface $input, OutputInterface &$output): void;
}
