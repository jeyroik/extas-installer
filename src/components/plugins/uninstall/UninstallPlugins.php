<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;

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

    /**
     * Rewrite this, cause we can not lean on theory of repo-get extension is existing.
     * @param array $item
     */
    protected function runStage(array &$item): void
    {
        $repo = new PluginRepository();
        $repo->delete([], $item);
        $this->infoLn(['Uninstalled item from plugins.']);
    }
}
