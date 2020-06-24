<?php
namespace extas\components;

use extas\interfaces\IHasClass;

/**
 * Trait THasClassHolder
 *
 * @package extas\components
 * @author jeyroik@gmail.com
 */
trait THasClassHolder
{
    /**
     * @param string $className
     * @return IHasClass
     */
    protected function getClassHolder(string $className)
    {
        return new class ([IHasClass::FIELD__CLASS => $className]) extends Item implements IHasClass {
            use THasClass;
            protected function getSubjectForExtension(): string
            {
                return '';
            }
        };
    }
}
