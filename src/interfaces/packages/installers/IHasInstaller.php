<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\packages\IInstaller;

/**
 * Interface IHasInstaller
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IHasInstaller
{
    public const FIELD__INSTALLER = 'installer';

    /**
     * @return IInstaller|null
     */
    public function getInstaller(): ?IInstaller;
}
