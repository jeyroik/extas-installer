<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasIO;
use extas\interfaces\IHasItemData;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallItem extends IHasName, IHasClass, IHasIO, IHasItemData
{
    public const NAME = 'extas.install.item';

    /**
     * @param array $item
     * @param IItem|null $existed
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array $item, ?IItem $existed, IInstaller &$installer): void;
}
