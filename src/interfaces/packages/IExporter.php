<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IHasPath;
use extas\interfaces\IItem;

/**
 * Interface IExporter
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IExporter extends IItem, IHasOutput, IHasInput, IHasPath
{
    public const SUBJECT = 'extas.exporter';

    /**
     * @param string $fileName
     * @param array $entitiesNames
     *
     * @return bool
     */
    public function exportTo(string $fileName, array $entitiesNames = []): bool;

    /**
     * @param array $entitiesNames
     * @param bool $asJson
     *
     * @return array|string
     */
    public function export(array $entitiesNames = [], bool $asJson = false);
}
