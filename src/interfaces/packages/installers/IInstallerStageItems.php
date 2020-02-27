<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\packages\installers\IHasInstaller;
use extas\interfaces\packages\installers\IHasOutput;
use extas\interfaces\packages\installers\IHasPlugin;

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
