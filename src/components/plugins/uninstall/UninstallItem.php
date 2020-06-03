<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\THasClass;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\components\THasRepository;
use extas\components\THasSection;
use extas\components\THasUid;
use extas\interfaces\stages\IStageUninstalledItem;
use extas\interfaces\stages\IStageUninstallItem;

/**
 * Class UninstallItem
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallItem extends Plugin implements IStageUninstallItem
{
    use THasInput;
    use THasOutput;
    use THasClass;
    use THasUid;
    use THasSection;
    use THasRepository;

    /**
     * @param array $item
     * @throws \Exception
     */
    public function __invoke(array &$item): void
    {
        $itemObject = $this->buildClassWithParameters($item);
        $deleted = $this->getRepository()->delete([], $itemObject);
        $deleted && $this->infoLn(['Deleted item from "' . $this->getSection() . '"']);

        foreach ($this->getPluginsByStage(IStageUninstalledItem::STAGE, $this->__toArray()) as $plugin) {
            /**
             * @var IStageUninstalledItem $plugin
             */
            $plugin($item);
        }
    }
}
