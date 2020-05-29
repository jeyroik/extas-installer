<?php
namespace extas\components\packages;

use extas\components\extensions\ExtensionRepository;
use extas\components\Plugins;
use extas\components\plugins\PluginInstallPackageClasses;
use extas\components\plugins\PluginRepository;
use extas\components\THasExtensions;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\components\THasPlugins;
use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IHasPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageInitialize;
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
    use THasInput;
    use THasOutput;

    protected array $packageConfig;
    protected OutputInterface $output;
    protected IRepository $pluginRepo;
    protected IRepository $extRepo;
    protected array $config = [];

    /**
     * Initializer constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param array $packages
     */
    public function run(array $packages): void
    {
        $this->pluginRepo = new PluginRepository();
        $this->extRepo = new ExtensionRepository();

        $this->installInterfaces($packages);

        foreach ($packages as $packageName => $package) {
            $this->initPackage($packageName, $package);
        }
    }

    /**
     * @param string $packageName
     * @param array $package
     */
    protected function initPackage(string $packageName, array $package): void
    {
        $this->writeLn(['', 'Initializing package "' . $packageName. '"...']);

        $this->config[IHasPlugins::FIELD__PLUGINS] = $package[IHasPlugins::FIELD__PLUGINS] ?? [];
        $this->config[IHasExtensions::FIELD__EXTENSIONS] = $package[IHasExtensions::FIELD__EXTENSIONS] ?? [];

        $this->installExtensions();
        $this->installPlugins();
        $this->runInitStages($packageName, $package);

        $this->writeLn(['', 'Package "' . $packageName. '" initialized.']);
    }

    /**
     * @param string $packageName
     * @param array $package
     */
    protected function runInitStages(string $packageName, array $package): void
    {
        $pluginConfig = [
            IHasInput::FIELD__INPUT => $this->getInput(),
            IHasOutput::FIELD__OUTPUT => $this->getOutput()
        ];

        $stage = IStageInitialize::NAME . '.' . $packageName;
        foreach (Plugins::byStage($stage, $this, $pluginConfig) as $plugin) {
            /**
             * @var IStageInitialize $plugin
             */
            $plugin($packageName, $package);
        }

        foreach (Plugins::byStage(IStageInitialize::NAME, $this, $pluginConfig) as $plugin) {
            /**
             * @var IStageInitialize $plugin
             */
            $plugin($packageName, $package);
        }
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
            $interfaceInstaller($package, $this->getOutput());
        }
        $interfaceInstaller->updateLockFile($this->getOutput());

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
}
