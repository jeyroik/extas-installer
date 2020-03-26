<?php
namespace extas\components\packages\entities;

use extas\components\repositories\Repository;
use extas\interfaces\packages\entities\IEntityRepository;

/**
 * Class EntityRepository
 *
 * @package extas\components\packages\entities
 * @author jeyroik <jeyroik@gmail.com>
 */
class EntityRepository extends Repository implements IEntityRepository
{
    protected string $name = 'entities';
    protected string $scope = 'extas';
    protected string $pk = Entity::FIELD__NAME;
    protected string $itemClass = Entity::class;
}
