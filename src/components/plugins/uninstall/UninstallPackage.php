<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\stages\IStageUninstalledPackage;
use extas\interfaces\stages\IStageUninstallPackage;
use extas\interfaces\stages\IStageUninstallSection;

/**
 * Class UninstallPackage
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallPackage extends Plugin implements IStageUninstallPackage
{
    use THasInput;
    use THasOutput;

    /**
     * @param string $packageName
     * @param array $package
     */
    public function __invoke(string $packageName, array &$package): void
    {
        $sectionName = $this->getInput()->getOption('section');
        if ($sectionName) {
            $this->uninstallSectionByName($sectionName, $package);
        } else {
            $this->uninstallSections($package);
        }

        $this->infoLn(['Uninstalled package ' . $packageName]);

        $this->runAfter($packageName, $package);
    }

    /**
     * @param string $sectionName
     * @param array $sections
     */
    protected function uninstallSectionByName(string $sectionName, array $sections): void
    {
        if (isset($sections[$sectionName])) {
            $sectionData = $sections[$sectionName];
            $this->runStage($sectionName, $sectionData, IStageUninstallSection::NAME . '.' . $sectionName);
        }
    }

    /**
     * @param array $sections
     */
    protected function uninstallSections(array $sections): void
    {
        foreach ($sections as $sectionName => $sectionData) {
            $this->uninstallSection($sectionName, $sectionData);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    protected function uninstallSection(string $sectionName, array $sectionData): void
    {
        if (!is_array($sectionData)) {
            $this->errorLn(['Skip section ' . $sectionName . ' - it is not an array.']);
        } else {
            $this->runStage($sectionName, $sectionData, IStageUninstallSection::NAME . '.' . $sectionName);
            $this->runStage($sectionName, $sectionData);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param string $stage
     */
    protected function runStage(
        string $sectionName,
        array &$sectionData,
        string $stage = IStageUninstallSection::NAME
    ): void
    {
        $pluginConfig = [
            IStageUninstallSection::FIELD__INPUT => $this->getInput(),
            IStageUninstallSection::FIELD__OUTPUT => $this->getOutput()
        ];

        foreach ($this->getPluginsByStage($stage, $pluginConfig) as $plugin) {
            /**
             * @var IStageUninstallSection $plugin
             */
            $plugin($sectionName, $sectionData);
        }
    }

    /**
     * @param string $packageName
     * @param array $package
     */
    protected function runAfter(string $packageName, array $package)
    {
        foreach ($this->getPluginsByStage(IStageUninstalledPackage::STAGE, $this->__toArray()) as $plugin) {
            /**
             * @var IStageUninstalledPackage $plugin
             */
            $plugin($packageName, $package);
        }
    }
}
