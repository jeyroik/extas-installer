<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasItemData;
use extas\interfaces\IHasName;
use extas\interfaces\IHasOutput;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageAfterInstallSection
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInstallSection extends IHasName, IHasClass, IHasInput, IHasOutput, IHasItemData
{
    public const NAME = 'extas.after.install.section';

    /**
     * @param array $sectionData
     * @param IInstaller $installer
     */
    public function __invoke(array $sectionData, IInstaller &$installer): void;
}
