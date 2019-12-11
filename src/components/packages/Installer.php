<?php
namespace extas\components\packages;

use extas\components\plugins\PluginInstallPackageClasses;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\extensions\IExtensionRepository;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\plugins\IPluginRepository;
use extas\interfaces\stages\IStage;
use extas\interfaces\stages\IStageRepository;
use extas\components\extensions\Extension;
use extas\components\plugins\Plugin;
use extas\components\stages\Stage;
use extas\components\Item;
use extas\components\SystemContainer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Installer
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Installer extends Item implements IInstaller
{
    protected $packageConfig = [];
    protected $generatedData = [];
    protected $many = false;
    protected $systemSettings = [
        self::FIELD__FLUSH
    ];

    /**
     * @param $packageConfigs array
     * @param $output OutputInterface
     *
     * @return bool|string
     */
    public function installMany($packageConfigs, $output)
    {
        $this->many = true;

        if($this->config[static::FIELD__REWRITE] ?? true) {
            $this->installInterfaces($packageConfigs, $output);
        }

        foreach ($packageConfigs as $packageConfig) {
            $this->install($packageConfig, $output);
        }

        foreach ($packageConfigs as $packageConfig) {
            $this->packageConfig = $packageConfig;
            foreach ($this->getPluginsByStage(static::STAGE__INSTALL) as $plugin) {
                $plugin($this, $output);
            }
        }

        return $output;
    }

    /**
     * @param $packageConfig array
     * @param $output OutputInterface
     *
     * @return bool|string
     */
    public function install($packageConfig, $output)
    {
        $this->prepareSettings($packageConfig);
        $this->packageConfig = $packageConfig;

        $packageName = $packageConfig['name'] ?? sha1(json_encode($packageConfig)) . ' (missed "name" section)';
        $output->writeln([
            '',
            'Package "' . $packageName. '" is installing...',
        ]);

        $this->applySettings($output)
            ->installStages($output)
            ->installPlugins($output)
            ->installExtensions($output);

        if (!$this->many) {
            foreach ($this->getPluginsByStage(static::STAGE__INSTALL) as $plugin) {
                $plugin($this, $output);
            }
        }

        return $output;
    }

    /**
     * @param $packagesConfigs array
     * @param $output OutputInterface
     *
     * @return bool|string
     */
    public function uninstallMany($packagesConfigs, $output)
    {
        $this->many = true;

        foreach ($packagesConfigs as $packageConfig) {
            $this->packageConfig = $packageConfig;
            foreach ($this->getPluginsByStage(static::STAGE__UNINSTALL) as $plugin) {
                $plugin($this, $output);
            }
        }

        foreach ($packagesConfigs as $packageConfig) {
            $this->uninstall($packageConfig, $output);
        }

        return $output;
    }

    /**
     * @param $packageConfig array
     * @param $output OutputInterface
     *
     * @return bool|string
     */
    public function uninstall($packageConfig, $output)
    {
        $this->packageConfig = $packageConfig;

        if (!$this->many) {
            foreach ($this->getPluginsByStage(static::STAGE__UNINSTALL) as $plugin) {
                $plugin($this, $output);
            }
        }

        $this->applySettings($output)
            ->uninstallExtensions($output)
            ->uninstallPlugins($output)
            ->uninstallStages($output);

        return $output;
    }

    /**
     * @return array
     */
    public function getGeneratedData()
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
    public function getPackageConfig()
    {
        return $this->packageConfig;
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function applySettings($output)
    {
        foreach ($this->packageConfig[static::FIELD__SETTINGS] as $setting => $options) {
            foreach ($this->getPluginsByStage('extas.install.setting.' . $setting) as $plugin) {
                $plugin($this, $output, $options);
            }
        }

        return $this;
    }

    /**
     * @param $servicesConfigs
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function installInterfaces($servicesConfigs, $output)
    {
        $interfaceInstaller = new PluginInstallPackageClasses();

        foreach ($servicesConfigs as $servicesConfig) {
            $this->packageConfig = $servicesConfig;
            $interfaceInstaller($this, $output);
        }
        $interfaceInstaller->updateLockFile($output);
        $this->packageConfig = null;

        return $this;
    }

    /**
     * @param $packageConfig
     */
    protected function prepareSettings(&$packageConfig)
    {
        $settings = $packageConfig[static::FIELD__SETTINGS] ?? [];

        foreach ($this->systemSettings as $setting) {
            if ($this->config[$setting]) {
                $settings[$setting] = $this->config[$setting];
            }
        }

        $packageConfig[static::FIELD__SETTINGS] = $settings;
    }

    /**
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function installStages($output)
    {
        /**
         * @var $stagesRepository IStageRepository
         */
        $stagesRepository = SystemContainer::getItem(IStageRepository::class);
        $stages = $this->packageConfig[static::FIELD__STAGES] ?? [];

        foreach ($stages as $stage) {
            if ($stagesRepository->one([IStage::FIELD__NAME => $stage[IStage::FIELD__NAME]])) {
                $output->writeln([
                    'Stage <info>"' . $stage[IStage::FIELD__NAME] . '"</info> is already installed.'
                ]);
            } else {
                $output->writeln([
                    '<info>Installing stage "' . $stage[IStage::FIELD__NAME] . '"...</info>'
                ]);
                $stage[IStage::FIELD__HAS_PLUGINS] = false;
                $stageObj = new Stage($stage);
                $stagesRepository->create($stageObj);
                $output->writeln([
                    '<info>Stage installed.</info>'
                ]);
            }
        }

        return $this;
    }

    /**
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function uninstallStages($output)
    {
        if (!$this->isMasked(static::FIELD__STAGES)) {
            return $this;
        }

        /**
         * @var $stagesRepository IStageRepository
         */
        $stagesRepository = SystemContainer::getItem(IStageRepository::class);
        $stages = $this->packageConfig[static::FIELD__STAGES] ?? [];

        foreach ($stages as $stage) {
            $stage = $stagesRepository->one([IStage::FIELD__NAME => $stage[IStage::FIELD__NAME]]);
            $stagesRepository->delete($stage);
            $output->writeln([
                'Stage <info>"' . $stage[IStage::FIELD__NAME] . '"</info> is uninstalled.'
            ]);
        }

        return $this;
    }

    /**
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function installPlugins($output)
    {
        /**
         * @var $pluginRepo IPluginRepository
         * @var $stageRepo IStageRepository
         * @var $stage IStage
         */
        $pluginRepo = SystemContainer::getItem(IPluginRepository::class);
        $stageRepo = SystemContainer::getItem(IStageRepository::class);
        $plugins = $this->packageConfig[static::FIELD__PLUGINS] ?? [];

        foreach ($plugins as $plugin) {
            $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';

            if (is_array($pluginStage)) {
                foreach ($pluginStage as $stage) {
                    $plugin[IPlugin::FIELD__STAGE] = $stage;
                    $this->installPlugin($output, $pluginRepo, $stageRepo, $plugin);
                }
            } else {
                $this->installPlugin($output, $pluginRepo, $stageRepo, $plugin);
            }
        }

        return $this;
    }

    /**
     * @param OutputInterface $output
     * @param IPluginRepository $pluginRepo
     * @param IStageRepository $stageRepo
     * @param array $plugin
     */
    protected function installPlugin($output, $pluginRepo, $stageRepo, $plugin)
    {
        $pluginClass = $plugin[IPlugin::FIELD__CLASS] ?? '';
        $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';

        if ($pluginRepo->one([
            IPlugin::FIELD__CLASS => $pluginClass,
            IPlugin::FIELD__STAGE => $pluginStage
        ])) {
            $output->writeln([
                'Plugin <info>"' . $pluginClass . '" [ ' . $pluginStage . ' ]</info> is already installed.'
            ]);
        } else {

            $output->writeln([
                '<info>Installing plugin "' . $pluginClass . '" [ ' . $pluginStage . ' ]...</info>'
            ]);
            $pluginObj = new Plugin($plugin);
            $pluginRepo->create($pluginObj);

            $stage = $stageRepo->one([IStage::FIELD__NAME => $pluginObj->getStage()]);
            $this->updateStageHasPlugins($stage, $pluginObj, $stageRepo);
            $output->writeln([
                '<info>Plugin installed.</info>'
            ]);
        }
    }

    /**
     * @param $stage IStage|null
     * @param $pluginObj IPlugin
     * @param $stageRepo IStageRepository
     *
     * @return $this
     */
    protected function updateStageHasPlugins($stage, $pluginObj, $stageRepo)
    {
        if (!$stage) {
            $stage = new Stage([
                IStage::FIELD__NAME => $pluginObj->getStage(),
                IStage::FIELD__HAS_PLUGINS => true
            ]);
            $stageRepo->create($stage);
        } else {
            $stage->setHasPlugins(true);
            $stageRepo->update($stage);
        }

        return $this;
    }

    /**
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function uninstallPlugins($output)
    {
        if (!$this->isMasked(static::FIELD__PLUGINS)) {
            return $this;
        }

        /**
         * @var $pluginRepo IPluginRepository
         */
        $pluginRepo = SystemContainer::getItem(IPluginRepository::class);
        $plugins = $this->packageConfig[static::FIELD__PLUGINS] ?? [];

        foreach ($plugins as $plugin) {
            $plugin = $pluginRepo->one([IPlugin::FIELD__CLASS => $plugin[IPlugin::FIELD__CLASS]]);
            $pluginRepo->delete($plugin);
            $output->writeln([
                'Plugin <info>"' . $plugin[IPlugin::FIELD__CLASS] . '"</info> uninstalled.'
            ]);
        }

        return $this;
    }

    /**
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function installExtensions($output)
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
                    $this->installExtension($output, $extensionRepo, $extension);
                }
            } else {
                $this->installExtension($output, $extensionRepo, $extension);
            }
        }

        return $this;
    }

    /**
     * @param OutputInterface $output
     * @param IExtensionRepository $extensionRepo
     * @param array $extension
     */
    protected function installExtension($output, $extensionRepo, $extension)
    {
        $extClass = $extension[IExtension::FIELD__CLASS] ?? '';
        $extSubject = $extension[IExtension::FIELD__SUBJECT] ?? '';

        if ($extensionRepo->one([
            IExtension::FIELD__CLASS => $extClass,
            IExtension::FIELD__SUBJECT => $extSubject
        ])) {
            $output->writeln([
                'Extension <info>"' . $extClass . '" [ ' . $extSubject . ' ]</info> is already installed.'
            ]);
        } else {
            $output->writeln([
                '<info>Installing extension "' . $extClass . '" [ ' . $extSubject . ' ]...</info>'
            ]);
            $extensionObj = new Extension($extension);
            $extensionRepo->create($extensionObj);
            $output->writeln([
                '<info>Extension installed.</info>'
            ]);
        }
    }

    /**
     * @param $output OutputInterface
     *
     * @return $this
     */
    protected function uninstallExtensions($output)
    {
        if (!$this->isMasked(static::FIELD__EXTENSIONS)) {
            return $this;
        }

        /**
         * @var $extensionRepo IExtensionRepository
         */
        $extensionRepo = SystemContainer::getItem(IExtensionRepository::class);
        $extensions = $this->packageConfig[static::FIELD__EXTENSIONS] ?? [];

        foreach ($extensions as $extension) {
            $extensionObj = $extensionRepo->one([IExtension::FIELD__CLASS => $extension[IExtension::FIELD__CLASS]]);
            $extensionRepo->delete($extensionObj);
            $output->writeln([
                'Extension <info>"' . $extension[IExtension::FIELD__CLASS] . '"</info> uninstalled.'
            ]);
        }

        return $this;
    }

    /**
     * @param $subject
     *
     * @return bool
     */
    public function isMasked($subject): bool
    {
        return (strpos($subject, $this->getOptionMask()) !== false)
            || ($this->getOptionMask() == static::OPTION__MASK__ANY);
    }

    /**
     * @return string
     */
    public function getOptionMask()
    {
        return $this->config[static::OPTION__MASK] ?? '*';
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
