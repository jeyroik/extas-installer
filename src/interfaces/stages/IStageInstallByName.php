<?php
namespace extas\interfaces\stages;

/**
 * Interface IStageInstallByName
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallByName
{
    /**
     * @param array $packages
     * @param array $generatedData
     * @return bool
     */
    public function __invoke(array &$packages, array &$generatedData): bool;
}
