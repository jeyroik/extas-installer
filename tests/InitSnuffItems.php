<?php
namespace tests;

use extas\components\items\SnuffItem;
use extas\components\plugins\init\InitSection;

/**
 * Class InitSnuffItems
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class InitSnuffItems extends InitSection
{
    protected string $selfSection = 'snuff_items';
    protected string $selfName = 'snuff item';
    protected string $selfRepositoryClass = 'unknown';
    protected string $selfUID = 'id';
    protected string $selfItemClass = SnuffItem::class;
}
