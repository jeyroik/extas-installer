<?php
namespace extas\components\plugins\init;

use extas\components\plugins\Plugin;
use extas\components\THasIndex;
use extas\components\THasIO;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\stages\IStageInitialize;
use extas\interfaces\stages\IStageInitializeSection;

/**
 * Class Init
 *
 * @package extas\components\plugins\init
 * @author jeyroik <jeyroik@gmail.com>
 */
class Init extends Plugin implements IStageInitialize
{
    use THasIO;
    use THasIndex;

    /**
     * @param string $packageName
     * @param array $package
     */
    public function __invoke(string $packageName, array $package): void
    {
        $index = $this->getIndex($package, 'init');

        foreach ($index as $sectionName) {
            $sectionData = $package[$sectionName] ?? [];
            if (!is_array($sectionData)) {
                $this->writeLn(['Skip section "' . $sectionName . '": content is not applicable.']);
                continue;
            }
            $this->initSection($sectionName, $sectionData);
        }
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    protected function initSection(string $sectionName, array $sectionData): void
    {
        $this->writeLn(['Initializing section "' . $sectionName . '"...']);

        $stage = IStageInitializeSection::NAME . '.' . $sectionName;
        foreach ($this->getPluginsByStage($stage, $this->getIO()) as $plugin) {
            $plugin($sectionName, $sectionData);
        }

        foreach ($this->getPluginsByStage(IStageInitializeSection::NAME, $this->getIO()) as $plugin) {
            $plugin($sectionName, $sectionData);
        }

        $this->writeLn(['Section "' . $sectionName . '" initialized.']);
    }
}
