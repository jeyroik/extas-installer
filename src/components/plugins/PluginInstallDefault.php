<?php
namespace extas\components\plugins;

use extas\components\packages\installers\InstallerOptions;
use extas\interfaces\IHasClass;
use extas\interfaces\IItem;
use extas\interfaces\packages\ICrawler;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\installers\IInstallerStageItem;
use extas\interfaces\packages\installers\IInstallerStageItems;
use extas\interfaces\plugins\IPluginInstallDefault;
use extas\interfaces\repositories\IRepository;
use extas\components\SystemContainer;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginInstallDefault
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
abstract class PluginInstallDefault extends Plugin implements IPluginInstallDefault
{
    use TInstallMessages;

    protected const STAGE__ITEMS = 'items';
    protected const STAGE__ITEM = 'item';

    protected string $selfSection = '';
    protected string $selfName = '';
    protected string $selfRepositoryClass = '';
    protected string $selfUID = '';
    protected string $selfItemClass = '';

    protected bool $isRewriteAllowed = true;

    /**
     * @param $installer IInstaller
     * @param $output OutputInterface
     */
    public function __invoke($installer, $output)
    {
        $serviceConfig = $installer->getPackageConfig();

        /**
         * @var $repo IRepository
         */
        $repo = SystemContainer::getItem($this->selfRepositoryClass);
        $items = $this->getItemsByOptions($installer, $output);
        empty($items) && $items = $serviceConfig[$this->selfSection] ?? [];

        foreach ($items as $item) {
            $operated = $this->operateItemByOptions($installer, $output, $item);

            if ($operated) {
                continue;
            }

            $uid = $this->getUidValue($item, $serviceConfig);

            if ($existed = $this->findItem($item, $serviceConfig, $repo)) {
                $theSame = true;
                foreach ($item as $field => $value) {
                    if (isset($existed[$field]) && ($existed[$field] != $value)) {
                        $theSame = false;
                        $existed[$field] = $value;
                    }
                }
                if (!$theSame && $this->isRewriteAllow($serviceConfig)) {
                    $this->install($uid, $output, $existed->__toArray(), $repo, 'update');
                } else {
                    $this->alreadyInstalled($uid, $this->selfName, $output);
                }
            } else {
                $this->install($uid, $output, $item, $repo, 'create');
            }
        }

        $this->afterInstall($items, $repo, $output);
    }

    /**
     * @param array $item
     * @param array $serviceConfig
     * @param IRepository $repo
     *
     * @return IItem|null
     */
    protected function findItem($item, $serviceConfig, $repo): ?IItem
    {
        $uid = $this->getUidValue($item, $serviceConfig);

        return $repo->one([$this->selfUID => $uid]);
    }

    /**
     * @param IInstaller $installer
     * @param OutputInterface $output
     * @param array $item
     *
     * @return bool
     */
    protected function operateItemByOptions($installer, $output, $item)
    {
        $operated = false;
        foreach(InstallerOptions::byStage(static::STAGE__ITEM, $installer->getInput()) as $option) {
            /**
             * @var $option IHasClass
             */
            $option->buildClassWithParameters([
                IInstallerStageItem::FIELD__INSTALLER => $installer,
                IInstallerStageItem::FIELD__PLUGIN => $this,
                IInstallerStageItem::FIELD__OUTPUT => $output,
                IInstallerStageItem::FIELD__ITEM => $item
            ]);

            $operated = $option();
        }

        return $operated;
    }

    /**
     * @param IInstaller $installer
     * @param OutputInterface $output
     *
     * @return array
     */
    protected function getItemsByOptions($installer, $output)
    {
        $items = [];
        foreach(InstallerOptions::byStage(static::STAGE__ITEMS, $installer->getInput()) as $option) {
            /**
             * @var $option IHasClass
             */
            $option->buildClassWithParameters([
                IInstallerStageItems::FIELD__INSTALLER => $installer,
                IInstallerStageItems::FIELD__PLUGIN => $this,
                IInstallerStageItems::FIELD__OUTPUT => $output
            ]);

            $items = $option();
        }

        return $items;
    }

    /**
     * @param $config
     *
     * @return bool
     */
    public function isRewriteAllow($config): bool
    {
        if (is_null($this->isRewriteAllowed)) {
            if (isset($config[ICrawler::FIELD__SETTINGS])) {
                $settings = $config[ICrawler::FIELD__SETTINGS];
                $this->isRewriteAllowed = $settings[ICrawler::SETTING__REWRITE_ALLOW] ?? false;
            }
        }

        return $this->isRewriteAllowed;
    }

    /**
     * @param string $uid
     * @param OutputInterface $output
     * @param array $item
     * @param IRepository $repo
     * @param string $method
     */
    public function install($uid, $output, $item, $repo, $method = 'create')
    {
        $this->installing($uid, $this->selfName, $output);
        $itemClass = $this->selfItemClass;
        $itemObj = new $itemClass($item);
        $repo->$method($itemObj);
        $this->installed($uid, $this->selfName, $output, $method);
    }

    /**
     * @param $items array
     * @param $repo IRepository
     * @param $output OutputInterface
     */
    public function afterInstall($items, $repo, $output)
    {
        // You can do something here
    }

    /**
     * @param $item
     * @param $packageConfig
     *
     * @return string
     */
    public function getUidValue(&$item, $packageConfig): string
    {
        return $item[$this->selfUID];
    }

    /**
     * @return string
     */
    public function getPluginItemClass(): string
    {
        return $this->selfItemClass;
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return $this->selfName;
    }

    /**
     * @return string
     */
    public function getPluginRepositoryInterface(): string
    {
        return $this->selfRepositoryClass;
    }

    /**
     * @return string
     */
    public function getPluginSection(): string
    {
        return $this->selfSection;
    }

    /**
     * @return string
     */
    public function getPluginUidField(): string
    {
        return $this->selfUID;
    }
}
