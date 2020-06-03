<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\stages\IStageUninstalledSection;
use extas\interfaces\stages\IStageUninstallItem;
use extas\interfaces\stages\IStageUninstallSection;

/**
 * Class UninstallSection
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallSection extends Plugin implements IStageUninstallSection
{
    use THasInput;
    use THasOutput;

    protected string $selfSection = '';
    protected string $selfName = '';
    protected string $selfRepositoryClass = '';
    protected string $selfUID = '';
    protected string $selfItemClass = '';

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    public function __invoke(string $sectionName, array &$sectionData): void
    {
        foreach ($sectionData as $item) {
            $this->runStage($item);
        }

        $this->infoLn(['Uninstalled section ' . $sectionName]);

        foreach ($this->getPluginsByStage(IStageUninstalledSection::STAGE, $this->__toArray()) as $plugin) {
            /**
             * @var IStageUninstalledSection $plugin
             */
            $plugin($sectionName, $sectionData);
        }
    }

    /**
     * @param array $item
     */
    protected function runStage(array &$item): void
    {
        $pluginConfig = [
            IStageUninstallItem::FIELD__INPUT => $this->getInput(),
            IStageUninstallItem::FIELD__OUTPUT => $this->getOutput(),
            IStageUninstallItem::FIELD__CLASS => $this->selfItemClass,
            IStageUninstallItem::FIELD__UID => $this->selfUID,
            IStageUninstallItem::FIELD__REPOSITORY => $this->selfRepositoryClass,
            IStageUninstallItem::FIELD__SECTION => $this->selfSection
        ];

        foreach ($this->getPluginsByStage(IStageUninstallItem::NAME, $pluginConfig) as $plugin) {
            /**
             * @var IStageUninstallItem $plugin
             */
            $plugin($item);
        }
    }
}
