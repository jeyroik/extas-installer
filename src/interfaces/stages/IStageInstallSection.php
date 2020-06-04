<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallSection
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallSection extends IHasIO
{
    public const NAME = 'extas.install.section';

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(string $sectionName, array &$sectionData, IInstaller &$installer): void;
}
