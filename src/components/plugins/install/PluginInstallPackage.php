<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageAfterInstallPackage;
use extas\interfaces\stages\IStageInstallSection;
use extas\interfaces\stages\IStageInstallSectionByName;

/**
 * Class PluginInstallPackage
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallPackage extends Plugin
{
    use THasInput;
    use THasOutput;

    /**
     * @param array $package
     * @param IInstaller $installer
     */
    protected function runAfter(array $package, IInstaller &$installer): void
    {
        foreach ($this->getPluginsByStage(IStageAfterInstallPackage::class, [
            IStageAfterInstallPackage::FIELD__INPUT => $this->getInput(),
            IStageAfterInstallPackage::FIELD__OUTPUT => $this->getOutput()
        ]) as $plugin) {
            $plugin($package, $installer);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     */
    protected function installSection(string $sectionName, array $sectionData, IInstaller &$installer): void
    {
        $installer->getOutput()->writeln(['Installing section "' . $sectionName . '"...']);

        $operated = $this->runByName($sectionName, $sectionData, $installer);

        if (!$operated) {
            $this->run($sectionName, $sectionData, $installer);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     */
    protected function run(string $sectionName, array $sectionData, IInstaller &$installer): void
    {
        foreach ($this->getPluginsByStage(IStageInstallSection::NAME) as $plugin) {
            /**
             * @var IStageInstallSection $plugin
             */
            $plugin($sectionName, $sectionData, $installer);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     * @return bool
     */
    protected function runByName(string $sectionName, array $sectionData, IInstaller &$installer): bool
    {
        $operated = false;
        foreach ($this->getPluginsByStage(IStageInstallSection::NAME . $sectionName) as $plugin) {
            /**
             * @var IStageInstallSectionByName $plugin
             */
            $plugin($sectionData, $installer);
        }

        return $operated;
    }
}
