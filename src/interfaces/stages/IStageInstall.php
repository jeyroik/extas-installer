<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;

/**
 * Interface IStageInstall
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstall extends IHasInput, IHasOutput
{
    public const NAME = 'extas.install';

    /**
     * @param array $packages
     * @param array $generatedData
     */
    public function __invoke(array $packages, array &$generatedData): void;
}
