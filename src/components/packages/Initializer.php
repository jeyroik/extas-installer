<?php
namespace extas\components\packages;

use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\components\SystemContainer;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\extensions\IExtensionRepository;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Output\OutputInterface;

class Initializer implements IInitializer
{
    protected OutputInterface $output;
    protected array $packageConfig;
    protected IRepository $pluginRepo;
    protected IRepository $extRepo;

    public function run(array $packages, OutputInterface $output)
    {
        $this->output = $output;
        $this->pluginRepo = new PluginRepository();
        $this->extRepo = new ExtensionRepository();

        foreach ($packages as $package) {
            $this->initPackage($package);
        }
    }

    protected function initPackage(array $package)
    {
        $packageName = $package['name'] ?? 'Missed name';
        $this->output([
            '',
            'Package "' . $packageName. '" is initializing...',
        ]);
        $this->packageConfig = $package;

        $this->installPlugins()->installExtensions();
    }

    /**
     * @return $this
     */
    protected function installPlugins()
    {
        $plugins = $this->packageConfig['plugins'] ?? [];

        foreach ($plugins as $plugin) {
            $this->installPlugin($plugin);
        }

        return $this;
    }

    /**
     * @param $pluginStage
     * @param string $pluginClass
     */
    protected function installArrayPluginStage($pluginStage, string $pluginClass): void
    {
        if (is_array($pluginStage)) {
            foreach ($pluginStage as $stage) {
                $this->installPlugin([
                    IPlugin::FIELD__CLASS => $pluginClass,
                    IPlugin::FIELD__STAGE => $stage
                ]);
            }
        }
    }

    /**
     * @param string $pluginStage
     * @param $pluginClass
     */
    protected function installArrayPluginClass(string $pluginStage, $pluginClass): void
    {
        if (is_array($pluginClass)) {
            foreach ($pluginClass as $class) {
                $this->installPlugin([
                    IPlugin::FIELD__CLASS => $class,
                    IPlugin::FIELD__STAGE => $pluginStage
                ]);
            }
        }
    }

    /**
     * @param array $plugin
     */
    protected function installPlugin(array $plugin)
    {
        $pluginClass = $plugin[IPlugin::FIELD__CLASS] ?? '';
        $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';
        $installOn = $plugin['install_on'] ?? 'initialization';

        if (($pluginStage == 'extas.init') || ($installOn == 'initialization')) {
            $this->installArrayPluginStage($pluginStage, $pluginClass);
            $this->installArrayPluginClass($pluginStage, $pluginClass);
        }

        if ($this->pluginRepo->one([
            IPlugin::FIELD__CLASS => $pluginClass,
            IPlugin::FIELD__STAGE => $pluginStage
        ])) {
            $this->output([
                '[NOTICE] Plugin <info>"' . $pluginClass . '" [ ' . $pluginStage
                . ' ]</info> is already installed.'
            ]);
        } else {
            $this->installSinglePlugin($pluginStage, $pluginClass, $plugin);
        }
    }

    /**
     * @param string $pluginStage
     * @param string $pluginClass
     * @param array $plugin
     */
    protected function installSinglePlugin(string $pluginStage, string $pluginClass, array $plugin): void
    {
        try {
            $this->output([
                '<info>Installing plugin "' . $pluginClass . '" [ ' . $pluginStage . ' ]...</info>'
            ]);
            $pluginObj = new Plugin($plugin);
            $this->pluginRepo->create($pluginObj);

            $this->output(['[ CREATE ] <info>Plugin installed.</info>']);
        } catch (\Exception $e) {
            $this->output([
                '<error>[ ERROR ]</error> Can not install Plugin',
                '<error>Plugin class "' . $pluginClass . '"</error>',
                '<error>Plugin Stage "' . $pluginStage . '"</error>',
                '<error>Error: ' . $e->getMessage() . '</error>'
            ]);
        }
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

        if ($existed = $extensionRepo->one([
            IExtension::FIELD__CLASS => $extClass,
            IExtension::FIELD__SUBJECT => $extSubject
        ])) {
            $this->output([
                '[NOTICE] Extension <info>"' . $extClass . '" [ ' . $extSubject . ' ]</info> is already installed.'
            ]);
            $this->updateExtensionMethods($existed, $extension, $extensionRepo);
        } else {
            $this->output([
                '[INFO] <info>Installing extension "' . $extClass . '" [ ' . $extSubject . ' ]...</info>'
            ]);
            $extensionObj = new Extension($extension);
            $extensionRepo->create($extensionObj);
            $this->output([
                '[CREATE] <info>Extension installed.</info>'
            ]);
        }
    }

    /**
     * @param $messages
     */
    protected function output($messages)
    {
        $this->output->writeln($messages);
    }
}
