<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasId;
use extas\interfaces\IItem;

/**
 * Interface IPackageEntity
 *
 * @package extas\interfaces\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IPackageEntity extends IItem, IHasId
{
    public const SUBJECT = 'extas.package.entity';

    public const FIELD__PACKAGE = 'package';
    public const FIELD__ENTITY = 'entity';
    public const FIELD__QUERY = 'query';

    public function getPackage(): string;
    public function getEntity(): string;
    public function getQuery(): array;
}
