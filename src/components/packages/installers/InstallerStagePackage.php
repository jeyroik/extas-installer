<?php
namespace extas\components\packages\installers;

use extas\components\Item;
use extas\interfaces\packages\installers\IInstallerStagePackage;

/**
 * Class DispatcherStageItems
 *
 * @package extas\components\packages\installers\dispatchers
 * @author jeyroik@gmail.com
 */
abstract class InstallerStagePackage extends Item implements IInstallerStagePackage
{
    use THasInput;

    /**
     * @return array
     */
    public function getPackageConfig(): array
    {
        return $this->config[static::FIELD__PACKAGE_CONFIG] ?? [];
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.installer.option.stage.package';
    }
}
