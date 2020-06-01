<?php
namespace extas\components\plugins\install;

use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageInstallSection;

/**
 * Class PluginInstallSection
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallSectionAny extends PluginInstallSection implements IStageInstallSection
{
    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     * @throws \Exception
     */
    public function __invoke(string $sectionName, array $sectionData, IInstaller &$installer): void
    {
        foreach ($sectionData as $item) {
            $existed = $this->findExisted($item);
            $this->installItem($sectionName, $item, $existed, $installer);
        }

        $this->runAfter($sectionData, $installer);
    }
}
