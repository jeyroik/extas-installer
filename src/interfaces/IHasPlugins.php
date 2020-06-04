<?php
namespace extas\interfaces;

/**
 * Interface IHasPlugins
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasPlugins
{
    public const FIELD__PLUGINS = 'plugins';

    /**
     * @return $this
     */
    public function installPlugins();

    /**
     * @param array $plugin
     * @return bool
     */
    public function isAllowInstallPlugin(array $plugin): bool;
}
