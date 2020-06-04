<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\THasIO;
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
    use THasIO;

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

        $stage = IStageUninstalledSection::STAGE;
        foreach ($this->getPluginsByStage($stage, $this->getIO($this->__toArray())) as $plugin) {
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
        $pluginConfig = $this->getIO([
            IStageUninstallItem::FIELD__CLASS => $this->selfItemClass,
            IStageUninstallItem::FIELD__UID => $this->selfUID,
            IStageUninstallItem::FIELD__REPOSITORY => $this->selfRepositoryClass,
            IStageUninstallItem::FIELD__SECTION => $this->selfSection
        ]);

        foreach ($this->getPluginsByStage(IStageUninstallItem::NAME, $pluginConfig) as $plugin) {
            /**
             * @var IStageUninstallItem $plugin
             */
            $plugin($item);
        }
    }
}
