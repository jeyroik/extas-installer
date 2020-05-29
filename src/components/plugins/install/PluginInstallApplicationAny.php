<?php
namespace extas\components\plugins\install;

use extas\components\packages\Installer;
use extas\interfaces\stages\IStageInstall;

/**
 * Class PluginInstallApplicationAny
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallApplicationAny extends PluginInstallApplication implements IStageInstall
{
    /**
     * @param array $packages
     * @param array $generatedData
     */
    public function __invoke(array $packages, array &$generatedData): void
    {
        $installer = new Installer([
            Installer::FIELD__INPUT => $this->getInput(),
            Installer::FIELD__OUTPUT => $this->getOutput()
        ]);

        $installer->installPackages($packages);

        $generatedData = $installer->getGeneratedData();
    }
}
