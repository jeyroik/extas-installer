<?php
namespace extas\interfaces\stages;

use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallPackageByName
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallPackageByName
{
    /**
     * @param array $package
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array &$package, IInstaller &$installer): void;
}
