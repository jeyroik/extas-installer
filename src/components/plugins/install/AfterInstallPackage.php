<?php
namespace extas\components\plugins\install;

use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\stages\IStageAfterInstallPackage;

/**
 * Class AfterInstallPackage
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
abstract class AfterInstallPackage extends Plugin implements IStageAfterInstallPackage
{
    use THasIO;
}
