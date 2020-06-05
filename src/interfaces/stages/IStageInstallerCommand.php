<?php
namespace extas\interfaces\stages;

use extas\commands\DefaultCommand;

/**
 * Interface IStageInstallerCommand
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallerCommand
{
    public const NAME = 'extas.installer.command';

    /**
     * @return DefaultCommand
     */
    public function __invoke(): DefaultCommand;
}
