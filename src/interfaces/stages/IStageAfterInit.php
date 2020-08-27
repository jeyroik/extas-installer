<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;

/**
 * Interface IStageAfterInit
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInit extends IHasIO
{
    public const NAME = 'extas.after.init';

    /**
     * @param array $packages
     */
    public function __invoke(array $packages): void;
}
