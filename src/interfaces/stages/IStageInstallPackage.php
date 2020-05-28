<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallPackage
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallPackage extends IHasInput, IHasOutput
{
    public const NAME = 'extas.install.package';

    /**
     * @param array $package
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array $package, IInstaller &$installer): void;
}
