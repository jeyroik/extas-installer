<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasPlugins;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IInitializer
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IInitializer extends IHasPlugins, IHasExtensions
{
    public const FIELD__PACKAGE_NAME = 'name';
    public const FIELD__INSTALL_ON = 'install_on';

    public const ON__INITIALIZATION = 'initialization';
    public const ON__INSTALL = 'install';

    public const STAGE__INITIALIZATION = 'extas.init';

    /**
     * @param array $packages
     * @param OutputInterface $output
     */
    public function run(array $packages, OutputInterface $output): void;
}
