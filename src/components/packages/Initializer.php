<?php
namespace extas\components\packages;

use extas\components\extensions\ExtensionRepository;
use extas\components\plugins\PluginInstallPackageClasses;
use extas\components\plugins\PluginRepository;
use extas\components\THasExtensions;
use extas\components\THasPlugins;
use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Initializer
 *
 * @package extas\components\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class Initializer implements IInitializer
{
    use THasPlugins;
    use THasExtensions;

    protected array $packageConfig;
    protected OutputInterface $output;
    protected IRepository $pluginRepo;
    protected IRepository $extRepo;
    protected array $config = [];

    /**
     * @param array $packages
     * @param OutputInterface $output
     */
    public function run(array $packages, OutputInterface $output): void
    {
        $this->output = $output;
        $this->pluginRepo = new PluginRepository();
        $this->extRepo = new ExtensionRepository();

        $this->installInterfaces($packages);

        foreach ($packages as $package) {
            $this->initPackage($package);
        }
    }

    /**
     * @param array $package
     */
    protected function initPackage(array $package): void
    {
        $packageName = $package[static::FIELD__PACKAGE_NAME] ?? 'Missed name';
        $this->output([
            '',
            'Package "' . $packageName. '" is initializing...',
        ]);

        $this->config[IHasPlugins::FIELD__PLUGINS] = $package[IHasPlugins::FIELD__PLUGINS] ?? [];
        $this->config[IHasExtensions::FIELD__EXTENSIONS] = $package[IHasExtensions::FIELD__EXTENSIONS] ?? [];

        $this->installPlugins();
        $this->installExtensions();
    }

    /**
     * @param $packages
     *
     * @return $this
     * @throws
     */
    protected function installInterfaces(array $packages)
    {
        $interfaceInstaller = new PluginInstallPackageClasses();

        foreach ($packages as $package) {
            $interfaceInstaller($package, $this->output);
        }
        $interfaceInstaller->updateLockFile($this->output);

        return $this;
    }

    /**
     * @param array $extension
     * @return bool
     */
    public function isAllowInstallExtension(array $extension): bool
    {
        $installOn = $extension[static::FIELD__INSTALL_ON] ?? static::ON__INITIALIZATION;

        return $installOn == static::ON__INITIALIZATION;
    }

    /**
     * @param array $plugin
     * @return bool
     */
    public function isAllowInstallPlugin(array $plugin): bool
    {
        $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';
        $installOn = $plugin[static::FIELD__INSTALL_ON] ?? static::ON__INITIALIZATION;

        return ($pluginStage == static::STAGE__INITIALIZATION) || ($installOn == static::ON__INITIALIZATION);
    }

    /**
     * @param $messages
     */
    protected function output($messages)
    {
        $this->output->writeln($messages);
    }
}
