<?php
namespace extas\components\plugins\init;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasName;
use extas\interfaces\IHasOutput;
use extas\interfaces\stages\IStageInitialize;
use extas\interfaces\stages\IStageInitializeSection;

/**
 * Class PluginInit
 *
 * @package extas\components\plugins\init
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInit extends Plugin implements IStageInitialize
{
    use THasInput;
    use THasOutput;

    /**
     * @param string $packageName
     * @param array $package
     */
    public function __invoke(string $packageName, array $package): void
    {
        if (isset($package[IHasName::FIELD__NAME])) {
            unset($package[IHasName::FIELD__NAME]);
        }
        
        foreach ($package as $sectionName => $sectionData) {
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

        $pluginConfig = [
            IHasInput::FIELD__INPUT => $this->getInput(),
            IHasOutput::FIELD__OUTPUT => $this->getOutput()
        ];

        $stage = IStageInitializeSection::NAME . '.' . $sectionName;
        foreach ($this->getPluginsByStage($stage, $pluginConfig) as $plugin) {
            $plugin($sectionName, $sectionData);
        }

        foreach ($this->getPluginsByStage(IStageInitializeSection::NAME, $pluginConfig) as $plugin) {
            $plugin($sectionName, $sectionData);
        }

        $this->writeLn(['Section "' . $sectionName . '" initialized.']);
    }
}
