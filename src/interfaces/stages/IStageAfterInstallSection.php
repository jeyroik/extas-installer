<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasName;
use extas\interfaces\IHasOutput;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;

/**
 * Interface IStageAfterInstallSection
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageAfterInstallSection extends IHasName, IHasClass, IHasInput, IHasOutput
{
    public const NAME = 'extas.after.install.section';

    public const FIELD__REPOSITORY = 'repository';
    public const FIELD__UID = 'uid';
    public const FIELD__SECTION = 'section';

    /**
     * @param array $sectionData
     * @param IInstaller $installer
     */
    public function __invoke(array $sectionData, IInstaller &$installer): void;

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
