<?php
namespace extas\components\plugins\install;

use extas\interfaces\IHasName;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageInstallPackage;

/**
 * Class PluginInstallPackage
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallPackageAny extends PluginInstallPackage implements IStageInstallPackage
{
    /**
     * @param array $package
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array $package, IInstaller &$installer): void
    {
        foreach ($package as $sectionName => $sectionData) {
            if (is_array($sectionData)) {
                $this->installSection($sectionName, $sectionData, $installer);
            }
        }

        $this->runAfter($package, $installer);

        $packageName = $package[IHasName::FIELD__NAME] ?? '';
        $this->getOutput()->writeln(['Package ' . $packageName . ' is installed.']);
    }
}
