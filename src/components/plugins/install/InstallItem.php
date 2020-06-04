<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasClass;
use extas\components\THasIO;
use extas\components\THasItemData;
use extas\components\THasName;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageAfterInstallItem;
use extas\interfaces\stages\IStageCreateItem;
use extas\interfaces\stages\IStageInstallItem;
use extas\interfaces\stages\IStageItemSame;

/**
 * Class InstallItem
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallItem extends Plugin implements IStageInstallItem
{
    use THasIO;
    use THasClass;
    use THasName;
    use THasItemData;

    /**
     * @param array $item
     * @param IItem|null $existed
     * @param IInstaller $installer
     * @throws \Exception
     */
    public function __invoke(array $item, ?IItem $existed, IInstaller &$installer): void
    {
        $this->getOutput()->writeln(['Installing item "' . $this->getItemUid($item) . '"...']);

        if ($existed && $this->isTheSame($existed, $item)) {
            $this->getOutput()->writeln([
                $this->getHighName() . ' "' . $this->getItemUid($item) . '" is already installed.'
            ]);
        } else {
            $repo = $this->getRepository();
            $existed
                ? $this->createOrUpdateItem($item, 'update', $repo, $installer)
                : $this->createOrUpdateItem($item, 'create', $repo, $installer);
        }
    }

    /**
     * @param array $item
     * @param string $method
     * @param IRepository $repo
     * @param IInstaller $installer
     */
    protected function createOrUpdateItem(array $item, string $method, IRepository $repo, IInstaller &$installer): void
    {
        $operated = false;
        $pluginConfig = $this->getIO($this->__toArray());

        $stage = IStageCreateItem::NAME . '.' . $this->getDottedName();
        foreach ($this->getPluginsByStage($stage, $pluginConfig) as $plugin) {
            /**
             * @var IStageCreateItem $plugin
             */
            $operated = $plugin($item, $method, $installer);
        }

        if (!$operated) {
            $repo->$method($this->buildClassWithParameters($item));
        }

        $this->runAfter($pluginConfig, $item, $installer);
        $this->getOutput()->writeln([$this->getHighName() . ' is installed.']);
    }

    /**
     * @param array $pluginConfig
     * @param array $item
     * @param IInstaller $installer
     */
    protected function runAfter(array $pluginConfig, array $item, IInstaller &$installer): void
    {
        foreach ($this->getPluginsByStage(IStageAfterInstallItem::NAME, $pluginConfig) as $plugin) {
            /**
             * @var IStageAfterInstallItem $plugin
             */
            $plugin($item, $installer);
        }
    }

    /**
     * @param IItem $existed
     * @param array $current
     * @return bool
     */
    protected function isTheSame(IItem $existed, array $current): bool
    {
        $theSame = false;

        $stage = IStageItemSame::NAME . '.' . $this->getDottedName();
        foreach ($this->getPluginsByStage($stage, $this->getIO()) as $plugin) {
            if ($plugin($existed, $current, $theSame)) {
                break;
            }
        }

        if ($theSame) {
            return $theSame;
        }

        foreach ($this->getPluginsByStage(IStageItemSame::NAME, $this->getIO()) as $plugin) {
            if ($plugin($existed, $current, $theSame)) {
                break;
            }
        }

        return $theSame;
    }

    /**
     * @param array $item
     * @return string
     */
    protected function getItemUid(array $item)
    {
        return $item[$this->getUid()] ?? '';
    }

    /**
     * @return string
     */
    protected function getDottedName(): string
    {
        return str_replace(' ', '.', $this->getName());
    }

    /**
     * @return string
     */
    protected function getHighName(): string
    {
        return ucfirst($this->getName());
    }
}
