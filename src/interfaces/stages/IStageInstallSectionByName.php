<?php
namespace extas\interfaces\stages;

use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallSectionByName
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallSectionByName
{
    /**
     * @param array $sectionNData
     * @param IInstaller $installer can be used to pass generated data
     * @return bool
     */
    public function __invoke(array $sectionNData, IInstaller &$installer): bool;
}
