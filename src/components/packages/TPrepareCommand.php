<?php
namespace extas\components\packages;

use extas\components\Item;
use extas\interfaces\crawlers\ICrawler;
use extas\interfaces\IItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait TPrepareCommand
 *
 * @package extas\components\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
trait TPrepareCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $operation
     * @return array
     */
    protected function prepareCommand(InputInterface $input, OutputInterface $output, string $operation): array
    {
        $packageName = $input->getOption('package_filename');
        $appName = $input->getOption('application');

        $output->writeln(['Searching packages...']);

        /**
         * @var ICrawler[] $crawlers
         */
        $crawlers = $this->getExtasApplication()->crawlerRepository()->all([]);

        $packages = [];
        foreach ($crawlers as $crawler) {
            $crawler->addParametersByValues(['package_name' => $packageName]);
            $packages = array_merge($packages, $crawler->dispatch(getcwd(), $input, $output));
        }

        $output->writeln([
            'Found ' . count($packages) . ' packages.',
            $operation . ' application ' . $appName . ' with found packages...'
        ]);

        return $packages;
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
}
