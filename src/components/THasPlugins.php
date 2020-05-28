<?php
namespace extas\components;

use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\interfaces\IHasPlugins;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;

/**
 * Trait THasPlugins
 *
 * @property array $config
 * @method isAllowInstallPlugin(array $plugin): bool
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasPlugins
{
    protected IRepository $pluginRepo;
    protected array $installPluginMessages = [];

    /**
     * @return $this
     */
    public function installPlugins()
    {
        $this->pluginRepo = new PluginRepository();
        $plugins = $this->config[IHasPlugins::FIELD__PLUGINS] ?? [];

        foreach ($plugins as $plugin) {
            $this->installPlugin($plugin);
        }

        return $this;
    }

    /**
     * @param $pluginStage
     * @param string $pluginClass
     * @return bool
     */
    protected function installArrayPluginStage($pluginStage, string $pluginClass): bool
    {
        if (is_array($pluginStage)) {
            foreach ($pluginStage as $stage) {
                $this->installPlugin([
                    IPlugin::FIELD__CLASS => $pluginClass,
                    IPlugin::FIELD__STAGE => $stage
                ]);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $pluginStage
     * @param $pluginClass
     * @return bool
     */
    protected function installArrayPluginClass(string $pluginStage, $pluginClass): bool
    {
        if (is_array($pluginClass)) {
            foreach ($pluginClass as $class) {
                $this->installPlugin([
                    IPlugin::FIELD__CLASS => $class,
                    IPlugin::FIELD__STAGE => $pluginStage
                ]);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $pluginStage
     * @param string $pluginClass
     * @param array $plugin
     */
    protected function installSinglePlugin(string $pluginStage, string $pluginClass, array $plugin): void
    {
        if ($this->pluginRepo->one([
            IPlugin::FIELD__CLASS => $pluginClass,
            IPlugin::FIELD__STAGE => $pluginStage
        ])) {
            $this->outputInstallPlugin([
                '<info>NOTICE: Plugin "' . $pluginClass . '" [ ' . $pluginStage . ' ]</info> is already installed.'
            ]);
        } else {
            $this->createPlugin($pluginStage, $pluginClass, $plugin);
        }
    }

    /**
     * @param string $pluginStage
     * @param string $pluginClass
     * @param array $plugin
     */
    protected function createPlugin(string $pluginStage, string $pluginClass, array $plugin): void
    {
        try {
            $this->outputInstallPlugin([
                '<info>Installing plugin "' . $pluginClass . '" [ ' . $pluginStage . ' ]...</info>'
            ]);
            $pluginObj = new Plugin($plugin);
            $this->pluginRepo->create($pluginObj);

            $this->outputInstallPlugin(['<info>[ CREATE ] Plugin installed.</info>']);
        } catch (\Exception $e) {
            $this->outputInstallPlugin([
                '<error>ERROR:</error> Can not install Plugin',
                '<error>Plugin class "' . $pluginClass . '"</error>',
                '<error>Plugin Stage "' . $pluginStage . '"</error>',
                '<error>Error: ' . $e->getMessage() . '</error>'
            ]);
        }
    }

    /**
     * @param array $plugin
     * @return bool
     */
    protected function installPlugin(array $plugin): bool
    {
        $pluginClass = $plugin[IPlugin::FIELD__CLASS] ?? '';
        $pluginStage = $plugin[IPlugin::FIELD__STAGE] ?? '';

        if ($this->isAllowInstallPlugin($plugin)) {
            $this->installArrayPluginStage($pluginStage, $pluginClass) ||
            $this->installArrayPluginClass($pluginStage, $pluginClass) ||
            $this->installSinglePlugin($pluginStage, $pluginClass, $plugin);

            return true;
        }

        return false;
    }

    /**
     * @param array $messages
     * @return $this
     */
    protected function outputInstallPlugin(array $messages)
    {
        $this->installPluginMessages = array_merge($this->installPluginMessages, $messages);

        return $this;
    }
}
