<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;

/**
 * Interface IStageUninstall
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstall extends IHasInput , IHasOutput
{
    public const NAME = 'extas.uninstall';

    /**
     * @param array $packages
     */
    public function __invoke(array &$packages): void;
}
