<?php
namespace extas\interfaces\packages;

use extas\interfaces\IItem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IExporter
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IExporter extends IItem
{
    public const SUBJECT = 'extas.exporter';

    public const FIELD__PATH = 'path';

    /**
     * @param string $fileName
     * @param array $entitiesNames
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function exportTo(string $fileName, array $entitiesNames = [], OutputInterface $output = null): bool;

    /**
     * @param array $entitiesNames
     * @param bool $asJson
     * @param OutputInterface $output
     *
     * @return array|string
     */
    public function export(array $entitiesNames = [], bool $asJson = false, OutputInterface $output = null);

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param string $path
     *
     * @return IExporter
     */
    public function setPath(string $path): IExporter;
}
