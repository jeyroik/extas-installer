<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stagegs\IStageAfterInstallSection;
use extas\interfaces\stages\IStageInstallItem;

/**
 * Class PluginInstallSection
 * @package extas\components\plugins\install
 */
abstract class PluginInstallSection extends Plugin
{
    use THasInput;
    use THasOutput;

    protected string $selfSection = '';
    protected string $selfName = '';
    protected string $selfRepositoryClass = '';
    protected string $selfUID = '';
    protected string $selfItemClass = '';

    /**
     * @param array $sectionData
     * @param IInstaller $installer
     */
    protected function runAfter(array $sectionData, IInstaller &$installer): void
    {
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
     * @throws \Exception
     */
    protected function findExisted(array $item)
    {
        $repoName = $this->selfRepositoryClass;
        try {
            $repo = $this->$repoName();
            return $repo->one([$this->selfUID => $item[$this->selfUID] ?? '']);
        } catch (\Exception $e) {
            throw new \Exception('Missed item repository ' . $repoName);
        }
    }

    protected function installItem(array $item, IItem $existed, IInstaller &$installer)
    {
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
        return [
            IStageInstallItem::FIELD__NAME => $this->selfName,
            IStageInstallItem::FIELD__UID => $this->selfUID,
            IStageInstallItem::FIELD__CLASS => $this->selfItemClass,
            IStageInstallItem::FIELD__SECTION => $this->selfSection,
            IStageInstallItem::FIELD__REPOSITORY => $this->selfRepositoryClass,
            IStageInstallItem::FIELD__INPUT => $this->getInput(),
            IStageInstallItem::FIELD__OUTPUT => $this->getOutput()
        ];
    }
}
