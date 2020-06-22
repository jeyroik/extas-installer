<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\THasIndex;
use extas\components\THasIO;
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
    use THasIO;
    use THasIndex;

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

        $this->runAfter($packageName, $package);
    }

    /**
     * @param string $sectionName
     * @param array $sections
     */
    protected function uninstallSectionByName(string $sectionName, array $sections): void
    {
        if (isset($sections[$sectionName]) && is_array($sections[$sectionName])) {
            $sectionData = $sections[$sectionName];
            $this->infoLn(['Uninstalling section ' . $sectionName . '...']);
            $this->runStage($sectionName, $sectionData, IStageUninstallSection::NAME . '.' . $sectionName);
            $this->infoLn(['Uninstalled section ' . $sectionName . '.']);
        }
    }

    /**
     * @param array $sections
     */
    protected function uninstallSections(array $sections): void
    {
        $index = $this->getIndex($sections, 'uninstall');

        foreach ($index as $sectionName) {
            $sectionData = $sections[$sectionName] ?? [];
            if (!is_array($sectionData)) {
                $this->errorLn(['Skip section ' . $sectionName . ' - it is not an array.']);
                continue;
            }
            $this->uninstallSection($sectionName, $sectionData);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    protected function uninstallSection(string $sectionName, array $sectionData): void
    {
        $this->infoLn(['Uninstalling section ' . $sectionName . '...']);
        $this->runStage($sectionName, $sectionData, IStageUninstallSection::NAME . '.' . $sectionName);
        $this->runStage($sectionName, $sectionData);
        $this->infoLn(['Uninstalled section ' . $sectionName . '.']);
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
        foreach ($this->getPluginsByStage($stage, $this->getIO()) as $plugin) {
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
        $stage = IStageUninstalledPackage::STAGE;
        foreach ($this->getPluginsByStage($stage, $this->getIO($this->__toArray())) as $plugin) {
            /**
             * @var IStageUninstalledPackage $plugin
             */
            $plugin($packageName, $package);
        }
    }
}
