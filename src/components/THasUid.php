<?php
namespace extas\components;

use extas\interfaces\IHasUid;

/**
 * Trait THasUid
 *
 * @property array $config
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasUid
{
    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->config[IHasUid::FIELD__UID] ?? '';
    }
}
