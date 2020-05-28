<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasName;
use extas\interfaces\IHasOutput;
use extas\interfaces\IItem;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;

/**
 * Interface IStageInstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageInstallItem extends IHasName, IHasClass, IHasInput, IHasOutput
{
    public const NAME = 'extas.install.item';

    public const FIELD__REPOSITORY = 'repository';
    public const FIELD__UID = 'uid';
    public const FIELD__SECTION = 'section';

    /**
     * @param array $item
     * @param IItem|null $existed
     * @param IInstaller $installer can be used to pass generated data
     */
    public function __invoke(array $item, ?IItem $existed, IInstaller &$installer): void;

    /**
     * @return IRepository
     */
    public function getRepository(): IRepository;

    /**
     * @return string
     */
    public function getUid(): string;

    /**
     * @return string
     */
    public function getSection(): string;
}
