<?php
namespace tests\packages;

use extas\components\items\SnuffItem;
use extas\components\items\SnuffRepository;
use extas\components\plugins\PluginInstallDefault;

/**
 * Class InstallTests
 *
 * @package tests\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallTests extends PluginInstallDefault
{
    protected string $selfSection = 'tests';
    protected string $selfName = 'test';
    protected string $selfRepositoryClass = SnuffRepository::class;
    protected string $selfUID = 'name';
    protected string $selfItemClass = SnuffItem::class;
}
