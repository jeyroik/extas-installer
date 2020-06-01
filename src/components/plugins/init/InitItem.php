<?php
namespace extas\components\plugins\init;

use extas\components\plugins\Plugin;
use extas\components\THasClass;
use extas\components\THasInput;
use extas\components\THasItemData;
use extas\components\THasName;
use extas\components\THasOutput;
use extas\interfaces\stages\IStageInitializeItem;

/**
 * Class InitItem
 *
 * @package extas\components\plugins\init
 * @author jeyroik <jeyroik@gmail.com>
 */
class InitItem extends Plugin implements IStageInitializeItem
{
    use THasItemData;
    use THasInput;
    use THasOutput;
    use THasName;
    use THasClass;

    /**
     * @param array $item
     * @throws \Exception
     */
    public function __invoke(array $item): void
    {
        $existed = $this->getRepository()->one([$this->getUid() => $item[$this->getUid()] ?? '']);
        $existed && $this->updateItem($item) || $this->createItem($item);
    }

    /**
     * @param array $item
     * @return bool
     * @throws \Exception
     */
    protected function updateItem(array $item): bool
    {
        $this->getRepository()->update($this->buildClassWithParameters($item));

        return true;
    }

    /**
     * @param array $item
     * @return bool
     * @throws \Exception
     */
    protected function createItem(array $item): bool
    {
        $this->getRepository()->create($this->buildClassWithParameters($item));

        return true;
    }
}
