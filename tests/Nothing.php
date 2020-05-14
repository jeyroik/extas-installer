<?php
namespace tests;

use extas\components\Item;

class Nothing extends Item
{
    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'nothing';
    }
}
