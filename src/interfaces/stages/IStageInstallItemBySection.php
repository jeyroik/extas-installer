<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;

/**
 * Interface IStageInstallItemBySection
 *
 * Stage name looks like: extas.install.section.<section.name>.item
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallItemBySection extends IHasIO
{
    /**
     * @param array $item
     * @param IItem|null $existed
     * @param IInstaller $installer
     */
    public function __invoke(array &$item, ?IItem $existed, IInstaller &$installer): void;
}
