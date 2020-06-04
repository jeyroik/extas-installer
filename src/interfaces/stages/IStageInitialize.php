<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;

/**
 * Interface IStageInitialize
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInitialize extends IHasIO
{
    public const NAME = 'extas.init';

    /**
     * @param string $packageName
     * @param array $package
     */
    public function __invoke(string $packageName, array $package): void;
}
