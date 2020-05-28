<?php
namespace extas\interfaces\packages;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IInitializer
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IInitializer
{
    public function run(array $packages, OutputInterface $output);
}
