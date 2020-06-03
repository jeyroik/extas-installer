<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;

/**
 * Interface IStageUninstallSection
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstallSection extends IHasInput, IHasOutput
{
    public const NAME = 'extas.uninstall.section';

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    public function __invoke(string $sectionName, array &$sectionData): void;
}
