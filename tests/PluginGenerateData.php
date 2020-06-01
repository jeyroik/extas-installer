<?php
namespace tests;

use extas\components\plugins\install\InstallPackage;
use extas\components\plugins\Plugin;
use extas\interfaces\packages\IInstaller;

/**
 * Class PluginGenerateData
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginGenerateData extends InstallPackage
{
    protected string $selfSection = 'plugins';
    protected string $selfName = 'plugin';
    protected string $selfRepositoryClass = 'pluginRepo';
    protected string $selfUID = Plugin::FIELD__CLASS;
    protected string $selfItemClass = Plugin::class;

    public function __invoke(array &$package, IInstaller &$installer): void
    {
        parent::__invoke($package, $installer);
        $installer->addGeneratedData('test', 'is ok');
    }
}
