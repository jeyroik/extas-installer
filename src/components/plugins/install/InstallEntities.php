<?php
namespace extas\components\plugins\install;

use extas\components\packages\entities\Entity;
use extas\interfaces\packages\entities\IEntityRepository;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stagegs\IStageAfterInstallSection;

/**
 * Class InstallEntities
 *
 * @method packageEntityRepository()
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallEntities extends AfterInstallSection implements IStageAfterInstallSection
{
    /**
     * @param array $sectionData
     * @param IInstaller $installer
     * @throws \Exception
     */
    public function __invoke(array $sectionData, IInstaller &$installer): void
    {
        /**
         * @var $entityRepo IEntityRepository
         */
        $entityRepo = $this->packageEntityRepository();
        $entity = $entityRepo->one([Entity::FIELD__NAME => $this->getSection()]);

        if (!$entity) {
            $this->writeLn(['Installing package entity "' . $this->getSection() . '"...']);

            $entity = new Entity([
                Entity::FIELD__NAME => $this->getSection(),
                Entity::FIELD__CLASS => get_class($this->getRepository())
            ]);
            $entityRepo->create($entity);

            $this->writeln(['Package entity "' . $this->getSection() . '" installed.']);
        }
    }
}
