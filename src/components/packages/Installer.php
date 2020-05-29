<?php
namespace extas\components\packages;

use extas\components\packages\installers\InstallerOptions;
use extas\components\THasExtensions;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\components\THasPlugins;
use extas\interfaces\IHasClass;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\installers\IInstallerStagePackage;
use extas\components\Item;
use extas\interfaces\stages\IStageInstallPackage;
use extas\interfaces\stages\IStageInstallPackageByName;

/**
 * Class Installer
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Installer extends Item implements IInstaller
{
    use THasInput;
    use THasOutput;

    protected array $package = [];
    protected array $generatedData = [];

    /**
     * @param $packages array
     *
     * @return bool
     */
    public function installPackages(array $packages): bool
    {
        foreach ($packages as $packageName => $package) {
            $this->installPackage($packageName, $package);
        }

        return true;
    }

    /**
     * @param string $packageName
     * @param array $package
     * @return bool
     */
    public function installPackage(string $packageName, array $package): bool
    {
        $this->package = $package;
        $this->output(['', 'Installing package "' . $packageName. '"...']);

        $operated = $this->operatePackageByOptions($package);

        if ($operated) {
            return true;
        }

        $operated = $this->runByName($packageName, $package);

        if ($operated) {
            return true;
        }

        $this->run($package);

        return true;
    }

    /**
     * @param array $package
     */
    protected function run(array $package): void
    {
        foreach ($this->getPluginsByStage(IStageInstallPackage::NAME) as $plugin) {
            /**
             * @var IStageInstallPackage $plugin
             */
            $plugin($package, $this);
        }
    }

    /**
     * @param string $packageName
     * @param array $package
     * @return bool
     */
    protected function runByName(string $packageName, array $package): bool
    {
        $operated = false;
        foreach ($this->getPluginsByStage(IStageInstallPackage::NAME . '.' . $packageName) as $plugin) {
            /**
             * @var IStageInstallPackageByName $plugin
             */
            $operated = $plugin($package, $this);
            if ($operated) {
                break;
            }
        }

        return $operated;
    }

    /**
     * @param array $package
     *
     * @return bool
     * @throws 
     */
    protected function operatePackageByOptions(array $package)
    {
        $operated = false;

        foreach (InstallerOptions::byStage(InstallerOptions::STAGE__PACKAGE, $this->getInput()) as $option) {
            /**
             * @var $option IHasClass
             */
            $option->buildClassWithParameters([
                IInstallerStagePackage::FIELD__INSTALLER => $this,
                IInstallerStagePackage::FIELD__PACKAGE_CONFIG => $package
            ]);

            $operated = $option();
        }

        return $operated;
    }

    /**
     * @return array
     */
    public function getGeneratedData(): array
    {
        return $this->generatedData;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function addGeneratedData($name, $value)
    {
        $this->generatedData[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getPackage(): array
    {
        return $this->package;
    }

    /**
     * @param array $plugin
     * @return bool
     */
    public function isAllowInstallPlugin(array $plugin): bool
    {
        $installOn = $plugin[IInitializer::FIELD__INSTALL_ON] ?? IInitializer::ON__INITIALIZATION;

        return $installOn == IInitializer::ON__INSTALL;
    }

    /**
     * @param array $extension
     * @return bool
     */
    public function isAllowInstallExtension(array $extension): bool
    {
        $installOn = $extension[IInitializer::FIELD__INSTALL_ON] ?? IInitializer::ON__INITIALIZATION;

        return $installOn == IInitializer::ON__INSTALL;
    }

    /**
     * @param $messages
     */
    protected function output($messages)
    {
        $this->config[static::FIELD__OUTPUT]->writeln($messages);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
