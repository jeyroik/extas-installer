<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageAfterInstallPackage
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInstallPackage extends IHasInput, IHasOutput
{
    public const NAME = 'extas.after.install.package';

    /**
     * @param array $package
     * @param IInstaller $installer
     */
    public function __invoke(array $package, IInstaller &$installer): void;
}
