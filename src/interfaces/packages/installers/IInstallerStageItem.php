<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\packages\installers\IHasInstaller;
use extas\interfaces\packages\installers\IHasOutput;
use extas\interfaces\packages\installers\IHasPlugin;

/**
 * Interface IInstallerStageItem
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerStageItem extends IHasInstaller, IHasPlugin, IHasOutput
{
    public const FIELD__ITEM = 'item';

    /**
     * Return bool is item operated
     *
     * @return bool
     */
    public function __invoke(): bool;

    /**
     * @return array
     */
    public function getItem(): array;
}
