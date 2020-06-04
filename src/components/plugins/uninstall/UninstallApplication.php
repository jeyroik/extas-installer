<?php
namespace extas\components\plugins\uninstall;

use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\stages\IStageUninstall;
use extas\interfaces\stages\IStageUninstalled;
use extas\interfaces\stages\IStageUninstallPackage;

/**
 * Class UninstallApplication
 *
 * @package extas\components\plugins\uninstall
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallApplication extends Plugin implements IStageUninstall
{
    use THasIO;

    /**
     * @param array $packages
     * @throws \Exception
     */
    public function __invoke(array &$packages): void
    {
        $packageName = $this->getInput()->getOption('package');
        if ($packageName) {
            $this->uninstallPackageByName($packageName, $packages);
        } else {
            $this->uninstallPackages($packages);
        }

        foreach ($this->getPluginsByStage(IStageUninstalled::STAGE, $this->getIO($this->__toArray())) as $plugin){
            /**
             * @var IStageUninstalled $plugin
             */
            $plugin($packages);
        }
    }

    /**
     * @param string $packageName
     * @param array $packages
     * @throws \Exception
     */
    protected function uninstallPackageByName(string $packageName, array $packages): void
    {
        if (isset($packages[$packageName])) {
            $package = $packages[$packageName];
            $this->infoLn(['Uninstalling package ' . $packageName . '...']);
            $this->runStage($packageName, $package, IStageUninstallPackage::NAME . '.' . $packageName);
            $this->infoLn(['Uninstalled package ' . $packageName . '.']);
        } else {
            throw new \Exception('Unknown package ' . $packageName);
        }
    }

    /**
     * @param array $packages
     */
    protected function uninstallPackages(array $packages): void
    {
        foreach ($packages as $packageName => $package) {
            $this->uninstallPackage($packageName, $package);
        }
    }

    /**
     * @param string $packageName
     * @param array $package
     */
    protected function uninstallPackage(string $packageName, array $package): void
    {
        $this->infoLn(['Uninstalling package ' . $packageName . '...']);
        $this->runStage($packageName, $package, IStageUninstallPackage::NAME . '.' . $packageName);
        $this->runStage($packageName, $package);
        $this->infoLn(['Uninstalled package ' . $packageName . '.']);
    }

    /**
     * @param string $packageName
     * @param array $package
     * @param string $stage
     */
    protected function runStage(string $packageName, array &$package, string $stage = ''): void
    {
        foreach ($this->getPluginsByStage($stage, $this->getIO()) as $plugin) {
            /**
             * @var IStageUninstallPackage $plugin
             */
            $plugin($packageName, $package);
        }
    }
}
