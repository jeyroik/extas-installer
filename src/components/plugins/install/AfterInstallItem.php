<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\components\THasItemData;
use extas\interfaces\stages\IStageAfterInstallItem;

/**
 * Class AfterInstallItem
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class AfterInstallItem extends Plugin implements IStageAfterInstallItem
{
    use THasIO;
    use THasItemData;
}
