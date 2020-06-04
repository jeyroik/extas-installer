<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;

/**
 * Interface IStageInstall
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstall extends IHasIO
{
    public const NAME = 'extas.install';

    /**
     * @param array $packages
     * @param array $generatedData
     */
    public function __invoke(array &$packages, array &$generatedData): void;
}
