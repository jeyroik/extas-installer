<?php
namespace tests;

use extas\components\plugins\install\InstallSection;
use extas\components\plugins\Plugin;
use extas\interfaces\packages\IInstaller;

/**
 * Class PluginGenerateData
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginGenerateData extends InstallSection
{
    protected string $selfSection = 'plugins';
    protected string $selfName = 'plugin';
    protected string $selfRepositoryClass = 'pluginRepo';
    protected string $selfUID = Plugin::FIELD__ID;
    protected string $selfItemClass = Plugin::class;

    /**
     * @param string $sectionName
     * @param array $sectionData
     * @param IInstaller $installer
     * @throws \Exception
     */
    public function __invoke(string $sectionName, array &$sectionData, IInstaller &$installer): void
    {
        parent::__invoke($sectionName, $sectionData, $installer);
        $installer->addGeneratedData('test', 'is ok');
    }
}
