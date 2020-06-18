<?php
namespace extas\components\plugins;

use extas\commands\DefaultCommand;
use extas\commands\UninstallCommand;
use extas\interfaces\stages\IStageInstallerCommand;

/**
 * Class PluginCommandUninstall
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginCommandUninstall extends Plugin implements IStageInstallerCommand
{
    /**
     * @return DefaultCommand
     */
    public function __invoke(): DefaultCommand
    {
        return new UninstallCommand();
    }
}
