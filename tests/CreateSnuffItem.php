<?php
namespace tests;

use extas\components\plugins\Plugin;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\stages\IStageCreateItem;

/**
 * Class CreateSnuffItem
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class CreateSnuffItem extends Plugin implements IStageCreateItem
{
    public static bool $worked = false;

    /**
     * @param array $item
     * @param string $method
     * @param IInstaller $installer
     * @return bool
     */
    public function __invoke(array $item, string $method, IInstaller &$installer): bool
    {
        self::$worked = true;

        return false;
    }
}
