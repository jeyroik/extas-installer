<?php
namespace extas\components\plugins;

use extas\components\packages\installers\InstallerOption;
use extas\components\plugins\install\InstallSection;
use extas\interfaces\packages\installers\IInstallerOptionRepository;

/**
 * Class InstallInstallerOptions
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class InstallInstallerOptions extends InstallSection
{
    protected string $selfItemClass = InstallerOption::class;
    protected string $selfName = 'installer option';
    protected string $selfSection = 'installer_options';
    protected string $selfUID = InstallerOption::FIELD__NAME;
    protected string $selfRepositoryClass = IInstallerOptionRepository::class;
}
