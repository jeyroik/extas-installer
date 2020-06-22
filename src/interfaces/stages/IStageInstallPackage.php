<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIndex;
use extas\interfaces\IHasIO;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallPackage
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallPackage extends IHasIO, IHasIndex
{
    public const NAME = 'extas.install.package';

    /**
     * @param array $package
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array &$package, IInstaller &$installer): void;
}
