<?php
namespace tests\commands\misc;

use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\stages\IStageAfterInit;

/**
 * Class PluginAfterInit
 *
 * @package tests\commands\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginAfterInit extends Plugin implements IStageAfterInit
{
    use THasIO;

    /**
     * @param array $packages
     */
    public function __invoke(array $packages): void
    {
        $this->getOutput()->writeln(['after init']);
    }
}
