<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;

/**
 * Interface IStageUninstall
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstall extends IHasIO
{
    public const NAME = 'extas.uninstall';

    /**
     * @param array $packages
     */
    public function __invoke(array &$packages): void;
}
