<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;

/**
 * Interface IStageInitializeSection
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInitializeSection extends IHasIO
{
    public const NAME = 'extas.init.section';

    /**
     * @param string $sectionName
     * @param array $sectionData
     */
    public function __invoke(string $sectionName, array $sectionData): void;
}
