<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasIO;
use extas\interfaces\IHasItemData;
use extas\interfaces\IHasName;

/**
 * Interface IStageInitializeItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInitializeItem extends IHasIO, IHasName, IHasClass, IHasItemData
{
    public const NAME = 'extas.init.item';

    /**
     * @param array $item
     */
    public function __invoke(array $item): void;
}
