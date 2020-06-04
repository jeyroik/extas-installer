<?php
namespace extas\components\plugins\install;

use extas\components\packages\Installer;
use extas\components\plugins\Plugin;
use extas\components\THasIO;
use extas\interfaces\stages\IStageInstall;

/**
 * Class PluginInstallApplication
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallApplication extends Plugin implements IStageInstall
{
    use THasIO;

    /**
     * @param array $packages
     * @param array $generatedData
     * @throws \Exception
     */
    public function __invoke(array &$packages, array &$generatedData): void
    {
        $installer = new Installer($this->getIO());
        $installer->installPackages($packages);

        $generatedData = $installer->getGeneratedData();
    }
}
