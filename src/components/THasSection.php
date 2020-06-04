<?php
namespace extas\components;

use extas\interfaces\IHasSection;

/**
 * Trait THasSection
 *
 * @property array $config
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasSection
{
    /**
     * @return string
     */
    public function getSection(): string
    {
        return $this->config[IHasSection::FIELD__SECTION] ?? '';
    }
}
