<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasItemData;
use extas\components\THasOutput;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageAfterInstallItem;

/**
 * Class AfterInstallItem
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class AfterInstallItem extends Plugin implements IStageAfterInstallItem
{
    use THasInput;
    use THasOutput;
    use THasItemData;
}
