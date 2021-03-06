<?php
namespace extas\components\packages;

use extas\components\extensions\ExtensionRepository;
use extas\components\Plugins;
use extas\components\plugins\PluginRepository;
use extas\components\THasExtensions;
use extas\components\THasIO;
use extas\components\THasPackageClasses;
use extas\components\THasPlugins;
use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IHasPackageClasses as IHasClasses;
use extas\interfaces\IHasPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageAfterInit;
use extas\interfaces\stages\IStageInitialize;

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
    use THasIO;
    use THasPackageClasses;

    protected array $packageConfig;
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
            $this->msgInit($packageName, 'core.init');
            $this->initCoreEntities($packageName, $package);
            $this->msgInit($packageName, 'core.initialized');
        }

        foreach ($packages as $packageName => $package) {
            $this->msgInit($packageName, 'secondary.init');
            $this->initSecondaryEntities($packageName, $package);
            $this->msgInit($packageName, 'secondary.initialized');
        }

        foreach (Plugins::byStage(IStageAfterInit::NAME, $this, $this->getIO()) as $plugin) {
            /**
             * @var IStageAfterInit $plugin
             */
            $plugin($packages);
        }
    }

    /**
     * @param string $packageName
     * @param string $msg
     */
    protected function msgInit(string $packageName, string $msg): void
    {
        $messages = [
            'core.init' => 'Initializing package "' . $packageName. '" core entities...',
            'core.initialized' => 'Package "' . $packageName. '" core entities initialized.',
            'secondary.init' => 'Initializing package "' . $packageName. '" secondary entities...',
            'secondary.initialized' => 'Package "' . $packageName. '" secondary entities initialized.'
        ];

        $this->commentLn(['', $messages[$msg], '']);
    }

    /**
     * @param string $packageName
     * @param array $package
     */
    protected function initSecondaryEntities(string $packageName, array $package): void
    {
        $this->runStage($packageName, $package, IStageInitialize::NAME . '.' . $packageName);
        $this->runStage($packageName, $package);
    }

    /**
     * @param string $packageName
     * @param array $package
     */
    protected function initCoreEntities(string $packageName, array $package): void
    {
        $this->config[IHasPlugins::FIELD__PLUGINS] = $package[IHasPlugins::FIELD__PLUGINS] ?? [];
        $this->config[IHasExtensions::FIELD__EXTENSIONS] = $package[IHasExtensions::FIELD__EXTENSIONS] ?? [];

        $this->commentLn(['Installing extensions...']);
        $this->installExtensions();

        $this->commentLn(['', 'Installing plugins...']);
        $this->installPlugins();
    }

    /**
     * @param string $packageName
     * @param array $package
     * @param string $stage
     */
    protected function runStage(string $packageName, array $package, string $stage  = IStageInitialize::NAME): void
    {
        $pluginConfig = [
            IHasInput::FIELD__INPUT => $this->getInput(),
            IHasOutput::FIELD__OUTPUT => $this->getOutput()
        ];

        foreach (Plugins::byStage($stage, $this, $pluginConfig) as $plugin) {
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
        foreach ($packages as $package) {
            $this->config[IHasClasses::FIELD__PACKAGE_CLASSES] = $package[IHasClasses::FIELD__PACKAGE_CLASSES] ?? [];
            $this->installPackageClasses();
        }
        $this->updateLockFile();

        return $this;
    }

    /**
     * @param array $extension
     * @return bool
     */
    public function isAllowInstallExtension(array $extension): bool
    {
        $installOn = $extension[IPlugin::FIELD__INSTALL_ON] ?? static::ON__INITIALIZATION;

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
