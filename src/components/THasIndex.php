<?php
namespace extas\components;

use extas\interfaces\IHasIndex;

/**
 * Trait THasIndex
 *
 * @package extas\components
 * @author jeyroik@gmail.com
 */
trait THasIndex
{
    /**
     * @param array $package
     * @param string $stage
     * @return array
     */
    public function getIndex(array $package, string $stage): array
    {
        $index = $package[IHasIndex::FIELD__INDEX] ?? [];

        return $index[$stage] ?? array_keys($package);
    }
}
