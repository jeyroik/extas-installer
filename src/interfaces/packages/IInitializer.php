<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IHasPlugins;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IInitializer
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IInitializer extends IHasPlugins, IHasExtensions, IHasInput, IHasOutput
{
    public const FIELD__PACKAGE_NAME = 'name';
    public const FIELD__INSTALL_ON = 'install_on';

    public const ON__INITIALIZATION = 'initialization';
    public const ON__INSTALL = 'install';

    public const STAGE__INITIALIZATION = 'extas.init';

    /**
     * IInitializer constructor.
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * @param array $packages
     */
    public function run(array $packages): void;
}
