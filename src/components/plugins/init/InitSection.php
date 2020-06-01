<?php
namespace extas\components\plugins\init;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
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
    public function __invoke(string $sectionName, array $sectionData): void
    {
        foreach ($sectionData as $item) {
            $this->initItem($item);
        }
    }

    /**
     * @param array $item
     */
    protected function initItem(array $item): void
    {
        $this->writeLn(['Initializing item...']);

        $pluginConfig = [
            IStageInitializeItem::FIELD__NAME => $this->selfName,
            IStageInitializeItem::FIELD__SECTION => $this->selfSection,
            IStageInitializeItem::FIELD__UID => $this->selfUID,
            IStageInitializeItem::FIELD__CLASS => $this->selfItemClass,
            IStageInitializeItem::FIELD__REPOSITORY => $this->selfRepositoryClass,
            IStageInitializeItem::FIELD__INPUT => $this->getInput(),
            IStageInitializeItem::FIELD__OUTPUT => $this->getOutput()
        ];

        foreach ($this->getPluginsByStage(IStageInitializeItem::NAME, $pluginConfig) as $plugin) {
            /**
             * @var IStageInitializeItem $plugin
             */
            $plugin($item);
        }

        $this->writeLn(['Item initialized.']);
    }
}
