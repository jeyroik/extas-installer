<?php
namespace extas\components\plugins;

use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;
use extas\components\SystemContainer;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginInstallListDefault
 *
 * @deprecated
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
abstract class PluginInstallListDefault extends Plugin
{
    use TInstallMessages;

    public string $preDefinedStage = 'extas.install';

    protected string $selfListSection = '';
    protected string $selfItemSection = 'items';

    protected string $selfListName = '';
    protected string $selfItemName = '';

    protected string $selfListRepositoryClass = '';
    protected string $selfItemRepositoryClass = '';

    protected string $selfListPK = '';
    protected string $selfItemPK = '';

    protected string $selfListClass = '';
    protected string $selfItemClass = '';

    /**
     * @param $installer IInstaller
     * @param $output OutputInterface
     */
    public function __invoke($installer, $output)
    {
        $packageConfig = $installer->getPackageConfig();

        /**
         * @var $listRepo IRepository
         * @var $itemRepo IRepository
         */
        $listRepo = SystemContainer::getItem($this->selfListRepositoryClass);
        $itemRepo = SystemContainer::getItem($this->selfItemRepositoryClass);
        $lists = $packageConfig[$this->selfListSection] ?? [];

        foreach ($lists as $list) {
            $listUid = $this->getListUidValue($list, $packageConfig);
            if ($listRepo->one([$this->selfListPK => $listUid])) {
                $this->alreadyInstalled($listUid, $this->selfListName, $output);
            } else {
                $this->installing($listUid, $this->selfListName, $output);

                $items = $list[$this->selfItemSection] ?? [];
                unset($list[$this->selfItemSection]);

                $listClass = $this->selfListClass;
                $listObj = new $listClass($list);
                $listRepo->create($listObj);

                foreach ($items as $item) {
                    $itemUid = $this->getItemUidValue($item, $packageConfig);
                    if ($itemRepo->one([$this->selfItemPK => $itemUid])) {
                        $this->alreadyInstalled($itemUid, $this->selfItemName, $output);
                    } else {
                        $item[$this->selfListName] = $listUid;
                        $itemClass = $this->selfItemClass;
                        $itemObj = new $itemClass($item);
                        $itemRepo->create($itemObj);
                        $this->installed($itemUid, $this->selfItemName, $output);
                    }
                }
                $this->installed($listUid, $this->selfListName, $output);
                $this->afterInstall($list, $items, $listRepo, $itemRepo, $output);
            }
        }
    }

    /**
     * @param $list array
     * @param $items array
     * @param $listRepo IRepository
     * @param $itemRepo IRepository
     * @param $output OutputInterface
     *
     * @param $repo
     */
    protected function afterInstall($list, $items, $listRepo, $itemRepo, $output)
    {
        // You can do something here
    }

    /**
     * @param $item
     * @param $packageConfig
     *
     * @return string
     */
    protected function getListUidValue(&$item, $packageConfig)
    {
        return $item[$this->selfListPK];
    }

    /**
     * @param $item
     * @param $packageConfig
     *
     * @return string
     */
    protected function getItemUidValue(&$item, $packageConfig)
    {
        return $item[$this->selfItemPK];
    }
}
