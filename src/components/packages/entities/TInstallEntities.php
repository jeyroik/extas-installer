<?php
namespace extas\components\packages\entities;

use extas\interfaces\packages\entities\IEntityRepository;
use extas\interfaces\repositories\IRepository;

/**
 * Trait TInstallEntities
 *
 * @method IRepository entityRepository()
 * @method void writeLn(array $lines)
 *
 * @package extas\components\packages\entities
 * @author jeyroik <jeyroik@gmail.com>
 */
trait TInstallEntities
{
    /**
     * @param string $sectionName
     * @param string $entityRepoName
     */
    public function installEntities(string $sectionName, string $entityRepoName): void
    {
        /**
         * @var $entityRepo IEntityRepository
         */
        $entityRepo = $this->entityRepository();
        $entity = $entityRepo->one([Entity::FIELD__NAME => $sectionName]);

        if (!$entity) {
            $this->writeLn(['Installing entity "' . $sectionName . '"...']);

            $entity = new Entity([
                Entity::FIELD__NAME => $sectionName,
                Entity::FIELD__CLASS => $entityRepoName
            ]);
            $entityRepo->create($entity);

            $this->writeln(['Entity "' . $sectionName . '" installed.']);
        }
    }
}
