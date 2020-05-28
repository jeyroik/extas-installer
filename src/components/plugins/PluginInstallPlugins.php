<?php
namespace extas\components\plugins;

use extas\components\plugins\install\PluginInstallSection;

/**
 * Class PluginInstallPlugins
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallPlugins extends PluginInstallSection
{
    protected string $selfSection = 'plugins';
    protected string $selfName = 'plugin';
    protected string $selfRepositoryClass = 'pluginRepository';
    protected string $selfUID = Plugin::FIELD__ID;
    protected string $selfItemClass = Plugin::class;
}
