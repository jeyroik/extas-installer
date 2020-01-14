<?php
namespace extas\components\packages\installers;

use extas\components\Item;
use extas\interfaces\packages\installers\IInstallerStageItems;

/**
 * Class DispatcherStageItems
 *
 * @package extas\components\packages\installers\dispatchers
 * @author jeyroik@gmail.com
 */
abstract class InstallerStageItems extends Item implements IInstallerStageItems
{
    use THasInput;
    use THasPlugin;
    use THasOutput;

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.installer.option.stage.items';
    }
}
