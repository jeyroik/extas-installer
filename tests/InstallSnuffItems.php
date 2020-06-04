<?php
namespace tests;

use extas\components\items\SnuffItem;
use extas\components\plugins\install\InstallSection;

/**
 * Class InstallSnuffItems
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallSnuffItems extends InstallSection
{
    protected string $selfSection = 'snuff_items';
    protected string $selfName = 'snuff item';
    protected string $selfRepositoryClass = 'snuffRepository';
    protected string $selfUID = 'name';
    protected string $selfItemClass = SnuffItem::class;
}
