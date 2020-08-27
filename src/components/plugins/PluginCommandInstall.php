<?php
namespace extas\components\plugins;

use extas\commands\DefaultCommand;
use extas\commands\InstallCommand;
use extas\interfaces\stages\IStageInstallerCommand;

/**
 * Class PluginCommandInstall
 *
 * @package extas\components\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginCommandInstall extends Plugin implements IStageInstallerCommand
{
    /**
     * @return DefaultCommand
     */
    public function __invoke(): DefaultCommand
    {
        return new InstallCommand();
    }
}
