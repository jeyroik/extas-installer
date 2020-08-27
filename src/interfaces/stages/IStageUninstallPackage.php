<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIndex;
use extas\interfaces\IHasIO;

/**
 * Interface IStageUninstallPackage
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstallPackage extends IHasIO, IHasIndex
{
    public const NAME = 'extas.uninstall.package';

    /**
     * @param string $packageName
     * @param array $package
     */
    public function __invoke(string $packageName, array &$package): void;
}
