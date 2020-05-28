<?php
namespace extas\components\plugins\install;

use extas\components\packages\Installer;
use extas\components\plugins\Plugin;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\stages\IStageInstall;

/**
 * Class PluginInstallApplication
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginInstallApplication extends Plugin implements IStageInstall
{
    use THasInput;
    use THasOutput;

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
