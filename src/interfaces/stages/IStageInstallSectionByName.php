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
     * @param array $sectionData
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array &$sectionData, IInstaller &$installer): void;
}
