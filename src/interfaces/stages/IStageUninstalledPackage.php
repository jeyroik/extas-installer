<?php
namespace extas\interfaces\stages;

/**
 * Interface IStageUninstalledPackage
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstalledPackage extends IStageUninstallPackage
{
    public const STAGE = 'extas.uninstalled.package';
}
