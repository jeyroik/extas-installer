<?php
namespace extas\interfaces\packages\entities;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;

/**
 * Interface IEntity
 *
 * @package extas\interfaces\packages\entities
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IEntity extends IItem, IHasName, IHasClass
{
    public const SUBJECT = 'extas.entity';
}
