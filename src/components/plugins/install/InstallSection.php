<?php
namespace extas\components\plugins\install;

use extas\components\exceptions\MissedOrUnknown;
use extas\components\Item;
use extas\components\packages\entities\TInstallEntities;
use extas\components\plugins\Plugin;
use extas\components\THasClass;
use extas\components\THasClassHolder;
use extas\components\THasIO;
use extas\interfaces\IHasClass;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageAfterInstallSection;
use extas\interfaces\stages\IStageInstallItem;
use extas\interfaces\stages\IStageInstallItemBySection;
use extas\interfaces\stages\IStageInstallSection;

/**
 * Class InstallSection
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallSection extends Plugin implements IStageInstallSection
{
    use THasIO;
    use TInstallEntities;
    use THasClassHolder;

    protected string $selfSection = '';
    protected string $selfName = '';
    protected string $selfRepositoryClass = '';
    protected string $selfUID = '';
    protected string $selfItemClass = '';

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     * @throws MissedOrUnknown
     */
    public function __invoke(string $sectionName, array &$sectionData, IInstaller &$installer): void
    {
        foreach ($sectionData as $item) {
            $existed = $this->findExisted($item);
            $this->installItem($sectionName, $item, $existed, $installer);
        }

        $this->runAfter($sectionName, $sectionData, $installer);
    }

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     */
    protected function runAfter(string $sectionName, array $sectionData, IInstaller &$installer): void
    {
        $this->installEntities($sectionName, $this->selfRepositoryClass);

        foreach ($this->getPluginsByStage(IStageAfterInstallSection::NAME, $this->getPluginConfig()) as $plugin) {
            /**
             * @var IStageAfterInstallSection $plugin
             */
            $plugin($sectionData, $installer);
        }
    }

    /**
     * @param array $item
     * @return mixed
     */
    protected function findExisted(array $item)
    {
        return $this->getClassHolder($this->selfRepositoryClass)
            ->buildClassWithParameters()
            ->one([$this->selfUID => $item[$this->selfUID] ?? '']);
    }

    /**
     * @param string $sectionName
     * @param array $item
     * @param IItem|null $existed
     * @param IInstaller $installer
     */
    protected function installItem(string $sectionName, array $item, ?IItem $existed, IInstaller &$installer): void
    {
        $stage = IStageInstallSection::NAME . '.' . $sectionName . '.item';
        foreach ($this->getPluginsByStage($stage, $this->getPluginConfig()) as $plugin) {
            /**
             * @var IStageInstallItemBySection $plugin
             */
            $plugin($item, $existed, $installer);
        }

        foreach ($this->getPluginsByStage(IStageInstallItem::NAME, $this->getPluginConfig()) as $plugin) {
            /**
             * @var IStageInstallItem $plugin
             */
            $plugin($item, $existed, $installer);
        }
    }

    /**
     * @return array
     */
    protected function getPluginConfig()
    {
        return $this->getIO([
            IStageInstallItem::FIELD__NAME => $this->selfName,
            IStageInstallItem::FIELD__UID => $this->selfUID,
            IStageInstallItem::FIELD__CLASS => $this->selfItemClass,
            IStageInstallItem::FIELD__SECTION => $this->selfSection,
            IStageInstallItem::FIELD__REPOSITORY => $this->selfRepositoryClass
        ]);
    }
}
