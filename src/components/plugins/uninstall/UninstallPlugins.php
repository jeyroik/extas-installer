<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;

/**
 * Class UninstallPlugins
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallPlugins extends UninstallSection
{
    protected string $selfSection = 'plugins';
    protected string $selfName = 'plugin';
    protected string $selfRepositoryClass = 'pluginRepository';
    protected string $selfUID = Plugin::FIELD__CLASS;
    protected string $selfItemClass = Plugin::class;
}
