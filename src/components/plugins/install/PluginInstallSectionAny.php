<?php
namespace extas\components\plugins\install;

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
     * @param array $sectionData
     * @param IInstaller $installer
     * @throws \Exception
     */
    public function __invoke(array $sectionData, IInstaller &$installer): void
    {
        foreach ($sectionData as $item) {
            $existed = $this->findExisted($item);
            $this->installItem($item, $existed, $installer);
        }

        $this->runAfter($sectionData, $installer);
    }

    protected function isAllowInstallItem(): bool
    {

    }
}
