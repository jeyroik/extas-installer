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
    protected string $selfItemClass = InstallerOption::class;
    protected string $selfName = 'installer option';
    protected string $selfSection = 'installer_options';
    protected string $selfUID = InstallerOption::FIELD__NAME;
    protected string $selfRepositoryClass = IInstallerOptionRepository::class;
}
