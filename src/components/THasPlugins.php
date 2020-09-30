<?php
namespace extas\components;

use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\interfaces\IHasPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\samples\parameters\ISampleParameter;

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
     * @param array $plugin
     * @return bool
     */
    protected function installArrayPluginStage($pluginStage, array $plugin): bool
    {
        if (is_array($pluginStage)) {
            foreach ($pluginStage as $stage) {
                $plugin[IPlugin::FIELD__STAGE]  = $stage;
                $this->installPlugin($plugin);
            }

            return true;
        }

        return false;
    }

    /**
     * @param array $plugin
     * @param $pluginClass
     * @return bool
     */
    protected function installArrayPluginClass(array $plugin, $pluginClass): bool
    {
        if (is_array($pluginClass)) {
            foreach ($pluginClass as $class) {
                $plugin[IPlugin::FIELD__CLASS] = $class;
                $this->installPlugin($plugin);
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
        $existed = $this->pluginRepo->one([
            Plugin::FIELD__HASH => $this->createPluginHash($plugin)
        ]);
        if ($existed) {
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

        $plugin[Plugin::FIELD__HASH] = $this->createPluginHash($plugin);
        $pluginObj = new Plugin($plugin);
        $this->pluginRepo->create($pluginObj);

        $this->writeLn(['<info>[ CREATE ] Plugin installed.</info>']);
    }

    /**
     * @param array $plugin
     * @return string
     */
    protected function createPluginHash(array $plugin): string
    {
        return sha1(json_encode($plugin));
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
            $this->installArrayPluginStage($pluginStage, $plugin) ||
            $this->installArrayPluginClass($plugin, $pluginClass) ||
            $this->installSinglePlugin($pluginStage, $pluginClass, $plugin);

            return true;
        }

        return false;
    }
}
