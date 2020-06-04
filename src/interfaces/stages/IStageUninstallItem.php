<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasIO;
use extas\interfaces\IHasItemData;

/**
 * Interface IStageUninstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstallItem extends IHasIO, IHasClass, IHasItemData
{
    public const NAME = 'extas.uninstall.item';

    public function __invoke(array &$item): void;
}
