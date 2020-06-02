<?php
namespace extas\components;

use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\interfaces\IHasPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;

/**
 * Trait THasPlugins
 *
 * @property array $config
 * @method bool isAllowInstallPlugin(array $plugin)
 * @method void writeLn(array $messages)
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasPlugins
{
    protected IRepository $pluginRepo;

    /**
     * @return $this
     */
    public function installPlugins()
    {
        $this->pluginRepo = new PluginRepository();
        $plugins = $this->config[IHasPlugins::FIELD__PLUGINS] ?? [];

        $this->writeLn(['Found ' . count($plugins) . ' plugin[s].']);

        foreach ($plugins as $plugin) {
            $this->installPlugin($plugin);
        }

        return $this;
    }

    /**
     * @param $pluginStage
     * @param string|array $pluginClass
     * @return bool
     */
    protected function installArrayPluginStage($pluginStage, $pluginClass): bool
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
     * @param string|array $pluginStage
     * @param $pluginClass
     * @return bool
     */
    protected function installArrayPluginClass($pluginStage, $pluginClass): bool
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
            $this->writeLn([
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
        $this->writeLn([
            '<info>Installing plugin "' . $pluginClass . '" [ ' . $pluginStage . ' ]...</info>'
        ]);

        if (isset($plugin[IInitializer::FIELD__INSTALL_ON])) {
            unset($plugin[IInitializer::FIELD__INSTALL_ON]);
        }

        $pluginObj = new Plugin($plugin);
        $this->pluginRepo->create($pluginObj);

        $this->writeLn(['<info>[ CREATE ] Plugin installed.</info>']);
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
}
