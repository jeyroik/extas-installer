<?php
namespace extas\components\packages\installers;

use extas\interfaces\packages\installers\IHasPlugin;
use extas\interfaces\plugins\IPluginInstallDefault;

/**
 * Trait THasPlugin
 *
 * @property $config
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
trait THasPlugin
{
    /**
     * @return IPluginInstallDefault|null
     */
    public function getPlugin(): ?IPluginInstallDefault
    {
        return $this->config[IHasPlugin::FIELD__PLUGIN] ?? null;
    }
}
