<?php
namespace extas\components\packages;

use extas\components\packages\installers\InstallerOptions;
use extas\components\plugins\PluginInstallPackageClasses;
use extas\interfaces\IHasClass;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\extensions\IExtensionRepository;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\packages\installers\IInstallerStagePackage;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\plugins\IPluginRepository;
use extas\components\extensions\Extension;
use extas\components\plugins\Plugin;
use extas\components\Item;
use extas\components\SystemContainer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Installer
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Installer extends Item implements IInstaller
{
    protected const STAGE__PACKAGE = 'package';

    protected array $packageConfig = [];
    protected array $generatedData = [];
    protected bool $many = false;

    protected ?IPluginRepository $pluginRepo = null;
    protected ?OutputInterface $output = null;

    /**
     * Installer constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->pluginRepo = SystemContainer::getItem(IPluginRepository::class);
    }

    /**
     * @param $packageConfigs array
     *
     * @return bool
     */
    public function installMany(array $packageConfigs): bool
    {
        $this->many = true;

        if($this->config[static::FIELD__REWRITE] ?? true) {
            $this->installInterfaces($packageConfigs);
        }

        foreach ($packageConfigs as $packageConfig) {
            $this->install($packageConfig);
        }

        foreach ($packageConfigs as $packageConfig) {
            $operated = $this->operatePackageByOptions($packageConfig);

            if ($operated) {
                continue;
            }

            $this->packageConfig = $packageConfig;

            foreach ($this->getPluginsByStage(static::STAGE__INSTALL) as $plugin) {
                $plugin($this, $this->getOutput());
            }
        }

        return true;
    }

    /**
     * @param array $packageConfig
     *
     * @return bool
     * @throws 
     */
    protected function operatePackageByOptions(array $packageConfig)
    {
        $operated = false;
        foreach (InstallerOptions::byStage(static::STAGE__PACKAGE, $this->getInput()) as $option) {
            /**
             * @var $option IHasClass
             */
            $option->buildClassWithParameters([
                IInstallerStagePackage::FIELD__INSTALLER => $this,
                IInstallerStagePackage::FIELD__PACKAGE_CONFIG => $packageConfig
            ]);

            $operated = $option();
        }

        return $operated;
    }

    /**
     * @param $packageConfig array
     * @return bool|string
     */
    public function install(array $packageConfig)
    {
        $this->packageConfig = $packageConfig;

        $packageName = $packageConfig['name'] ?? sha1(json_encode($packageConfig)) . ' (missed "name" section)';
        $this->output([
            '',
            'Package "' . $packageName. '" is installing...',
        ]);

        $this->installPlugins()
            ->installExtensions();

        if (!$this->many) {
            $operated = $this->operatePackageByOptions($packageConfig);
            if (!$operated) {
                foreach ($this->getPluginsByStage(static::STAGE__INSTALL) as $plugin) {
                    $plugin($this, $this->getOutput());
                }
            }
        }

        return true;
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
    public function getPackageConfig(): array
    {
        return $this->packageConfig;
    }

    /**
     * @param $servicesConfigs
     *
     * @return $this
     * @throws
     */
    protected function installInterfaces(array $servicesConfigs)
    {
        $interfaceInstaller = new PluginInstallPackageClasses();

        foreach ($servicesConfigs as $servicesConfig) {
            $this->packageConfig = $servicesConfig;
            $interfaceInstaller($this, $this->getOutput());
        }
        $interfaceInstaller->updateLockFile($this->getOutput());
        $this->packageConfig = [];

        return $this;
    }

    /**
     * @return $this
     */
    protected function installPlugins()
    {
        $plugins = $this->packageConfig[static::FIELD__PLUGINS] ?? [];

        foreach ($plugins as $plugin) {
            $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';

            if (is_array($pluginStage)) {
                foreach ($pluginStage as $stage) {
                    $plugin[IPlugin::FIELD__STAGE] = $stage;
                    $this->installPlugin($plugin);
                }
            } else {
                $this->installPlugin($plugin);
            }
        }

        return $this;
    }

    /**
     * @param array $plugin
     */
    protected function installPlugin(
        array $plugin
    )
    {
        $pluginClass = $plugin[IPlugin::FIELD__CLASS] ?? '';
        $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';

        if ($this->pluginRepo->one([
            IPlugin::FIELD__CLASS => $pluginClass,
            IPlugin::FIELD__STAGE => $pluginStage
        ])) {
            $this->output([
                'Plugin <info>"' . $pluginClass . '" [ ' . $pluginStage . ' ]</info> is already installed.'
            ]);
        } else {

            $this->output([
                '<info>Installing plugin "' . $pluginClass . '" [ ' . $pluginStage . ' ]...</info>'
            ]);
            $pluginObj = new Plugin($plugin);
            $this->pluginRepo->create($pluginObj);

            $this->output([
                '<info>Plugin installed.</info>'
            ]);
        }
    }

    /**
     * @return $this
     */
    protected function uninstallPlugins()
    {
        $plugins = $this->packageConfig[static::FIELD__PLUGINS] ?? [];

        foreach ($plugins as $plugin) {
            $plugin = $this->pluginRepo->one([IPlugin::FIELD__CLASS => $plugin[IPlugin::FIELD__CLASS]]);
            $this->pluginRepo->delete($plugin);
            $this->output([
                'Plugin <info>"' . $plugin[IPlugin::FIELD__CLASS] . '"</info> uninstalled.'
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function installExtensions()
    {
        /**
         * @var $extensionRepo IExtensionRepository
         */
        $extensionRepo = SystemContainer::getItem(IExtensionRepository::class);
        $extensions = $this->packageConfig[static::FIELD__EXTENSIONS] ?? [];

        foreach ($extensions as $extension) {
            $extSubject = $extension[IExtension::FIELD__SUBJECT] ?? '';

            if (is_array($extSubject)) {
                foreach ($extSubject as $subject) {
                    $extension[IExtension::FIELD__SUBJECT] = $subject;
                    $this->installExtension($extensionRepo, $extension);
                }
            } else {
                $this->installExtension($extensionRepo, $extension);
            }
        }

        return $this;
    }

    /**
     * @param IExtensionRepository $extensionRepo
     * @param array $extension
     */
    protected function installExtension(IExtensionRepository $extensionRepo, array $extension)
    {
        $extClass = $extension[IExtension::FIELD__CLASS] ?? '';
        $extSubject = $extension[IExtension::FIELD__SUBJECT] ?? '';

        if ($extensionRepo->one([
            IExtension::FIELD__CLASS => $extClass,
            IExtension::FIELD__SUBJECT => $extSubject
        ])) {
            $this->output([
                'Extension <info>"' . $extClass . '" [ ' . $extSubject . ' ]</info> is already installed.'
            ]);
        } else {
            $this->output([
                '<info>Installing extension "' . $extClass . '" [ ' . $extSubject . ' ]...</info>'
            ]);
            $extensionObj = new Extension($extension);
            $extensionRepo->create($extensionObj);
            $this->output([
                '<info>Extension installed.</info>'
            ]);
        }
    }

    /**
     * @return $this
     */
    protected function uninstallExtensions()
    {
        /**
         * @var $extensionRepo IExtensionRepository
         */
        $extensionRepo = SystemContainer::getItem(IExtensionRepository::class);
        $extensions = $this->packageConfig[static::FIELD__EXTENSIONS] ?? [];

        foreach ($extensions as $extension) {
            $extensionObj = $extensionRepo->one([IExtension::FIELD__CLASS => $extension[IExtension::FIELD__CLASS]]);
            $extensionRepo->delete($extensionObj);
            $this->output([
                'Extension <info>"' . $extension[IExtension::FIELD__CLASS] . '"</info> uninstalled.'
            ]);
        }

        return $this;
    }

    /**
     * @return null|InputInterface
     */
    public function getInput(): ?InputInterface
    {
        return $this->config[static::FIELD__INPUT] ?? null;
    }

    /**
     * @return OutputInterface|null
     */
    public function getOutput(): ?OutputInterface
    {
        return $this->config[static::FIELD__OUTPUT] ?? null;
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
