<?php
namespace extas\components\plugins;

use extas\components\packages\installers\InstallerOption;
use extas\interfaces\packages\installers\IInstallerOptionRepository;

/**
 * Class PluginInstallOptionsForInstaller
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallOptionsForInstaller extends PluginInstallDefault
{
    protected $selfItemClass = InstallerOption::class;
    protected $selfName = 'installer option';
    protected $selfSection = 'installer_options';
    protected $selfUID = InstallerOption::FIELD__NAME;
    protected $selfRepositoryClass = IInstallerOptionRepository::class;
}
