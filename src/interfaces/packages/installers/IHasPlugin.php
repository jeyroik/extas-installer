<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\plugins\IPluginInstallDefault;

/**
 * Interface IHasPlugin
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IHasPlugin
{
    public const FIELD__PLUGIN = 'plugin';

    /**
     * @return IPluginInstallDefault|null
     */
    public function getPlugin(): ?IPluginInstallDefault;
}
