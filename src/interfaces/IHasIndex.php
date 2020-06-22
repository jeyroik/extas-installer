<?php
namespace extas\interfaces;

/**
 * Interface IHasIndex
 *
 * @package extas\interfaces
 * @author jeyroik@gmail.com
 */
interface IHasIndex
{
    public const FIELD__INDEX = 'index';

    /**
     * @param array $package
     * @param string $stage
     * @return array
     */
    public function getIndex(array $package, string $stage): array;
}
