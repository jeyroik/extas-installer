<?php
namespace extas\interfaces\stages;

use extas\interfaces\IItem;

/**
 * Interface IStageItemSame
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageItemSame
{
    public const NAME = 'extas.item.same';

    /**
     * @param IItem $existed
     * @param array $current
     * @param bool $theSame
     * @return bool
     */
    public function __invoke(IItem $existed, array $current, bool $theSame): bool;
}
