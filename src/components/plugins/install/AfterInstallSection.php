<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasItemData;
use extas\components\THasName;
use extas\components\THasOutput;
use extas\interfaces\stages\IStageAfterInstallSection;

/**
 * Class AfterInstallSection
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class AfterInstallSection extends Plugin implements IStageAfterInstallSection
{
    use THasInput;
    use THasOutput;
    use THasName;
    use THasItemData;
}
