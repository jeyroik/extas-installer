<?php
namespace extas\components\packages\installers;

use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\installers\IHasInstaller;

/**
 * Trait THasInstaller
 *
 * @property $config
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
trait THasInstaller
{
    /**
     * @return IInstaller|null
     */
    public function getInstaller(): ?IInstaller
    {
        return $this->config[IHasInstaller::FIELD__INSTALLER] ?? null;
    }
}
