<?php
namespace tests;

use extas\components\items\SnuffItem;
use extas\components\plugins\install\InstallSection;

/**
 * Class CreateUnknown
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class CreateUnknown extends InstallSection
{
    protected string $selfSection = 'unknown_repo';
    protected string $selfName = 'unknown repo';
    protected string $selfRepositoryClass = 'unknown';
    protected string $selfUID = 'name';
    protected string $selfItemClass = SnuffItem::class;
}
