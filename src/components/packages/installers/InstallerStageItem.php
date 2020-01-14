<?php
namespace extas\components\packages\installers;

use extas\components\Item;
use extas\interfaces\packages\installers\IInstallerStageItem;

/**
 * Class DispatcherStageItem
 *
 * @package extas\components\packages\installers\dispatchers
 * @author jeyroik@gmail.com
 */
abstract class InstallerStageItem extends Item implements IInstallerStageItem
{
    use THasInput;
    use THasOutput;
    use THasPlugin;

    /**
     * @return array
     */
    public function getItem(): array
    {
        return $this->config[static::FIELD__ITEM] ?? [];
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.installer.option.stage.item';
    }
}
