<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\packages\installers\IHasInstaller;

/**
 * Interface IInstallerStagePackage
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerStagePackage extends IHasInstaller
{
    public const FIELD__PACKAGE_CONFIG = 'package_config';

    /**
     * Return bool is package operated
     *
     * @return bool
     */
    public function __invoke(): bool;

    /**
     * @return array
     */
    public function getPackageConfig(): array;
}
