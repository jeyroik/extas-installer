<?php
namespace extas\components\packages;

use extas\components\THasExtensions;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\components\THasPlugins;
use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\packages\IInstaller;
use extas\components\Item;
use extas\interfaces\stages\IStageInstallPackage;

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
    use THasPlugins;
    use THasExtensions;

    protected array $package = [];
    protected array $generatedData = [];

    /**
     * @param array $packages
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
        $this->writeLn(['', 'Installing package "' . $packageName. '"...']);
        $this->config[IHasPlugins::FIELD__PLUGINS] = $package[IHasPlugins::FIELD__PLUGINS] ?? [];
        $this->config[IHasExtensions::FIELD__EXTENSIONS] = $package[IHasExtensions::FIELD__EXTENSIONS] ?? [];

        $this->installExtensions();
        $this->installPlugins();

        $this->run($package, IStageInstallPackage::NAME . '.' . $packageName);
        $this->run($package);

        return true;
    }

    /**
     * @param array $package
     * @param string $stage
     */
    protected function run(array $package, string $stage = IStageInstallPackage::NAME): void
    {
        $pluginConfig = [
            IStageInstallPackage::FIELD__INPUT => $this->getInput(),
            IStageInstallPackage::FIELD__OUTPUT => $this->getOutput()
        ];
        
        foreach ($this->getPluginsByStage($stage, $pluginConfig) as $plugin) {
            /**
             * @var IStageInstallPackage $plugin
             */
            $plugin($package, $this);
        }
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
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
