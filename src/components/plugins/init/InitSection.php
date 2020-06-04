<?php
namespace extas\components\plugins\init;

use extas\components\packages\entities\TInstallEntities;
use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\stages\IStageInitializeItem;
use extas\interfaces\stages\IStageInitializeSection;

/**
 * Class InitSection
 *
 * @package extas\components\plugins\init
 * @author jeyroik <jeyroik@gmail.com>
 */
class InitSection extends Plugin implements IStageInitializeSection
{
    use THasIO;
    use TInstallEntities;

    protected string $selfSection = '';
    protected string $selfName = '';
    protected string $selfRepositoryClass = '';
    protected string $selfUID = '';
    protected string $selfItemClass = '';

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    public function __invoke(string $sectionName, array $sectionData): void
    {
        foreach ($sectionData as $item) {
            $this->initItem($item);
        }

        $this->installEntities($sectionName, $this->selfRepositoryClass);
    }

    /**
     * @param array $item
     */
    protected function initItem(array $item): void
    {
        $this->writeLn(['Initializing item...']);

        $pluginConfig = $this->getIO([
            IStageInitializeItem::FIELD__NAME => $this->selfName,
            IStageInitializeItem::FIELD__SECTION => $this->selfSection,
            IStageInitializeItem::FIELD__UID => $this->selfUID,
            IStageInitializeItem::FIELD__CLASS => $this->selfItemClass,
            IStageInitializeItem::FIELD__REPOSITORY => $this->selfRepositoryClass
        ]);

        foreach ($this->getPluginsByStage(IStageInitializeItem::NAME, $pluginConfig) as $plugin) {
            /**
             * @var IStageInitializeItem $plugin
             */
            $plugin($item);
        }

        $this->writeLn(['Item initialized.']);
    }
}
