<?php
namespace extas\components\packages\entities;

use extas\components\Item;
use extas\components\THasClass;
use extas\components\THasName;
use extas\interfaces\packages\entities\IEntity;

/**
 * Class Entity
 *
 * @package extas\components\packages\entities
 * @author jeyroik <jeyroik@gmail.com>
 */
class Entity extends Item implements IEntity
{
    use THasClass;
    use THasName;

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
