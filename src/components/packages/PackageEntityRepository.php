<?php
namespace extas\components\packages;

use extas\components\repositories\Repository;
use extas\interfaces\packages\IPackageEntityRepository;

/**
 * Class PackageEntityRepository
 *
 * @package extas\components\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class PackageEntityRepository extends Repository implements IPackageEntityRepository
{
    protected string $pk = PackageEntity::FIELD__ID;
    protected string $itemClass = PackageEntity::class;
    protected string $scope = 'extas';
    protected string $name = 'packages_entities';
}
