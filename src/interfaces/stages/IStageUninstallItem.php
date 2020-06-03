<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IHasRepository;
use extas\interfaces\IHasSection;
use extas\interfaces\IHasUid;

/**
 * Interface IStageUninstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageUninstallItem extends IHasInput, IHasOutput, IHasClass, IHasRepository, IHasUid, IHasSection
{
    public const NAME = 'extas.uninstall.item';

    public function __invoke(array &$item): void;
}
