<?php
namespace extas\interfaces\packages\installers;

/**
 * Interface IInstallerStagePackage
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerStagePackage
{
    const FIELD__INSTALLER = 'installer';
    const FIELD__PACKAGE_CONFIG = 'package_config';
    const FIELD__IS_OPERATED = 'operated';
}
