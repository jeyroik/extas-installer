<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageCreateItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageCreateItem extends IHasIO
{
    public const NAME = 'extas.create.item';

    /**
     * @param array $item
     * @param string $method
     * @param IInstaller $installer can be used to pass generated data
     * @return bool
     */
    public function __invoke(array $item, string $method, IInstaller &$installer): bool;
}
