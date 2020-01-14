<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\packages\installers\dispatchers\IHasInstaller;
use extas\interfaces\packages\installers\dispatchers\IHasOutput;
use extas\interfaces\packages\installers\dispatchers\IHasPlugin;

/**
 * Interface IInstallerStageItems
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerStageItems extends IHasInstaller, IHasPlugin, IHasOutput
{
    /**
     * Return items
     *
     * @return array
     */
    public function __invoke(): array;
}
