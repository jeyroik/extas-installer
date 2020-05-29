<?php
namespace extas\components\plugins;

use extas\components\plugins\install\PluginInstallSectionAny;

/**
 * Class PluginInstallPlugins
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallPluginsAny extends PluginInstallSectionAny
{
    protected string $selfSection = 'plugins';
    protected string $selfName = 'plugin';
    protected string $selfRepositoryClass = 'pluginRepository';
    protected string $selfUID = Plugin::FIELD__ID;
    protected string $selfItemClass = Plugin::class;
}
