<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;

/**
 * Interface IStageInitialize
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInitialize extends IHasInput, IHasOutput
{
    public const NAME = 'extas.init';

    /**
     * @param string $packageName
     * @param array $package
     */
    public function __invoke(string $packageName, array $package): void;
}
