<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;
use extas\interfaces\IHasItemData;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageAfterInstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInstallItem extends IHasIO, IHasItemData
{
    public const NAME = 'extas.after.install.item';

    /**
     * @param array $item
     * @param IInstaller $installer
     */
    public function __invoke(array $item, IInstaller &$installer): void;
}
