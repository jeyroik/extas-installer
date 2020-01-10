<?php
namespace extas\interfaces\packages\installers;

/**
 * Interface IInstallerStageItem
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerStageItem
{
    const FIELD__INSTALLER = 'installer';
    const FIELD__PLUGIN = 'plugin';
    const FIELD__OUTPUT = 'output';
    const FIELD__ITEM = 'item';
    const FIELD__IS_OPERATED = 'operated';
}
