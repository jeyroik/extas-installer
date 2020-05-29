<?php
namespace tests;

use extas\components\items\SnuffItem;
use extas\components\plugins\init\PluginInitSection;

/**
 * Class PluginInstallSnuffItemsOnInit
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallSnuffItemsOnInit extends PluginInitSection
{
    protected string $selfSection = 'snuff';
    protected string $selfName = 'snuffs';
    protected string $selfRepositoryClass = 'snuffRepository';
    protected string $selfUID = 'name';
    protected string $selfItemClass = SnuffItem::class;
}
