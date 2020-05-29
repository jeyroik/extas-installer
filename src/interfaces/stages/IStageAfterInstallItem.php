<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasItemData;
use extas\interfaces\IHasOutput;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageAfterInstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInstallItem extends IHasInput, IHasOutput, IHasItemData
{
    public const NAME = 'extas.after.install.item';

    /**
     * @param array $item
     * @param IInstaller $installer
     */
    public function __invoke(array $item, IInstaller &$installer): void;
}
