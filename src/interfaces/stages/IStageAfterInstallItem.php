<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;

/**
 * Interface IStageAfterInstallItem
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInstallItem extends IHasInput, IHasOutput
{
    public const NAME = 'extas.after.install.item';

    public const FIELD__REPOSITORY = 'repository';
    public const FIELD__UID = 'uid';
    public const FIELD__SECTION = 'section';

    /**
     * @param array $item
     * @param IInstaller $installer
     */
    public function __invoke(array $item, IInstaller &$installer): void;

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
