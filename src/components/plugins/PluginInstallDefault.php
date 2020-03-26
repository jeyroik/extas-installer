<?php
namespace extas\components\plugins;

use extas\components\packages\entities\Entity;
use extas\components\packages\installers\InstallerOptions;
use extas\components\packages\PackageEntity;
use extas\interfaces\IHasClass;
use extas\interfaces\IItem;
use extas\interfaces\packages\entities\IEntityRepository;
use extas\interfaces\packages\ICrawler;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\installers\IInstallerStageItem;
use extas\interfaces\packages\installers\IInstallerStageItems;
use extas\interfaces\packages\IPackageEntityRepository;
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
    protected array $packageConfig = [];

    /**
     * @param $installer IInstaller
     * @param $output OutputInterface
     * @throws \Exception
     */
    public function __invoke($installer, $output)
    {
        $this->packageConfig = $installer->getPackageConfig();

        /**
         * @var $repo IRepository
         */
        $repo = SystemContainer::getItem($this->selfRepositoryClass);

        if (!$repo || is_string($repo)) {
            throw new \Exception('Can not find repository "' . $this->selfRepositoryClass . '"');
        }

        $items = $this->getItemsByOptions($installer, $output);
        empty($items) && $items = $this->packageConfig[$this->selfSection] ?? [];

        foreach ($items as $item) {
            $operated = $this->operateItemByOptions($installer, $output, $item);

            if ($operated) {
                continue;
            }

            $uid = $this->getUidValue($item, $this->packageConfig);

            if ($existed = $this->findItem($item, $repo)) {
                $theSame = true;
                foreach ($item as $field => $value) {
                    if (isset($existed[$field]) && ($existed[$field] != $value)) {
                        $theSame = false;
                        $existed[$field] = $value;
                    }
                }
                if (!$theSame && $this->isRewriteAllow($this->packageConfig)) {
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
     * @param IRepository $repo
     *
     * @return IItem|null
     */
    protected function findItem($item, $repo): ?IItem
    {
        $uid = $this->getUidValue($item, $this->packageConfig);

        return $repo->one([$this->selfUID => $uid]);
    }

    /**
     * @param IInstaller $installer
     * @param OutputInterface $output
     * @param array $item
     *
     * @return bool
     * @throws
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
     * @throws
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
        $this->installPackageEntity($item);
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

    /**
     * @param array $item
     */
    protected function installPackageEntity(array $item): void
    {
        $packageEntity = new PackageEntity([
            PackageEntity::FIELD__PACKAGE => $this->packageConfig['name'] ?? 'unknown',
            PackageEntity::FIELD__ENTITY => $this->selfSection,
            PackageEntity::FIELD__QUERY => $this->getPackageEntityQuery($item),
            PackageEntity::FIELD__ID => '@uuid4'
        ]);

        /**
         * @var $repo IPackageEntityRepository
         */
        $repo = SystemContainer::getItem(IPackageEntityRepository::class);
        $repo->create($packageEntity);

        /**
         * @var $entityRepo IEntityRepository
         */
        $entityRepo = SystemContainer::getItem(IEntityRepository::class);
        $entity = $entityRepo->one([Entity::FIELD__NAME => $this->selfSection]);
        if (!$entity) {
            $entity = new Entity([
                Entity::FIELD__NAME => $this->selfSection,
                Entity::FIELD__CLASS => $this->selfRepositoryClass
            ]);
            $entityRepo->create($entity);
        }
    }

    /**
     * @param array $item
     * @return array
     */
    protected function getPackageEntityQuery(array $item)
    {
        return [
            $this->selfUID => $this->getUidValue($item, $this->packageConfig)
        ];
    }
}
