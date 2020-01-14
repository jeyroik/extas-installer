<?php
namespace extas\interfaces\plugins;

use extas\interfaces\packages\IInstaller;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IPluginInstallDefault
 *
 * @package extas\interfaces\plugins
 * @author jeyroik@gmail.com
 */
interface IPluginInstallDefault extends IPlugin
{
    /**
     * @param $installer IInstaller
     * @param $output OutputInterface
     */
    public function __invoke($installer, $output);

    /**
     * @param $config
     *
     * @return bool
     */
    public function isRewriteAllow($config): bool;

    /**
     * @param string $uid
     * @param OutputInterface $output
     * @param array $item
     * @param IRepository $repo
     * @param string $method
     */
    public function install($uid, $output, $item, $repo, $method = 'create');

    /**
     * @param $items array
     * @param $repo IRepository
     * @param $output OutputInterface
     */
    public function afterInstall($items, $repo, $output);

    /**
     * @param $item
     * @param $packageConfig
     *
     * @return string
     */
    public function getUidValue(&$item, $packageConfig): string;

    /**
     * @return string
     */
    public function getPluginItemClass(): string;

    /**
     * @return string
     */
    public function getPluginName(): string;

    /**
     * @return string
     */
    public function getPluginRepositoryInterface(): string;

    /**
     * @return string
     */
    public function getPluginSection(): string;

    /**
     * @return string
     */
    public function getPluginUidField(): string;
}
