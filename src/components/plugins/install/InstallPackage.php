<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasIndex;
use extas\components\THasIO;
use extas\interfaces\IHasName;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageAfterInstallPackage;
use extas\interfaces\stages\IStageInstallPackage;
use extas\interfaces\stages\IStageInstallSection;

/**
 * Class InstallPackage
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallPackage extends Plugin implements IStageInstallPackage
{
    use THasIO;
    use THasIndex;

    /**
     * @param array $package
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array &$package, IInstaller &$installer): void
    {
        $index = $this->getIndex($package, 'install');

        foreach ($index as $sectionName) {
            $sectionData = $package[$sectionName] ?? [];
            if (!is_array($sectionData)) {
                $this->writeLn(['Skip section "' . $sectionName . '": content is not applicable.']);
                continue;
            }
            $this->installSection($sectionName, $sectionData, $installer);
        }

        $this->runAfter($package, $installer);

        $packageName = $package[IHasName::FIELD__NAME] ?? '';
        $this->getOutput()->writeln(['Package ' . $packageName . ' is installed.']);
    }

    /**
     * @param array $package
     * @param IInstaller $installer
     */
    protected function runAfter(array $package, IInstaller &$installer): void
    {
        foreach ($this->getPluginsByStage(IStageAfterInstallPackage::NAME, $this->getIO()) as $plugin) {
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

        $this->run($sectionName, $sectionData, $installer, IStageInstallSection::NAME . '.' . $sectionName);
        $this->run($sectionName, $sectionData, $installer);
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     * @param string $stage
     */
    protected function run(
        string $sectionName,
        array $sectionData,
        IInstaller &$installer,
        string $stage = IStageInstallSection::NAME
    ): void
    {
        foreach ($this->getPluginsByStage($stage, $this->getIO()) as $plugin) {
            /**
             * @var IStageInstallSection $plugin
             */
            $plugin($sectionName, $sectionData, $installer);
        }
    }
}
