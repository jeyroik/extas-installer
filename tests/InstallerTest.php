<?php
namespace tests\packages;

use extas\interfaces\packages\IInstaller;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\packages\IInitializer;
use extas\components\plugins\PluginRepository;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\entities\EntityRepository;
use extas\components\plugins\PluginEmpty;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use extas\components\plugins\Plugin;
use extas\components\Plugins;
use extas\components\packages\Installer;

use Symfony\Component\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use tests\CanNotCreate;

/**
 * Class InstallerTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallerTest extends TestCase
{
    use TSnuffRepository;
    use TSnuffPlugins;
    use TSnuffConsole;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'pluginRepo' => PluginRepository::class,
            'entityRepository' => EntityRepository::class,
            'extRepo' => ExtensionRepository::class
        ]);
    }

    /**
     * Clean up
     */
    public function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testInstall()
    {
        $installer = $this->getInstaller();
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => PluginEmpty::class,
                        Plugin::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);

        $this->reloadSnuffPlugins();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }

        $this->assertEquals(1, PluginEmpty::$worked);

        $plugins = $this->allSnuffRepos('pluginRepo');
        $this->assertCount(1, $plugins, 'Incorrect plugins count');

        $plugin = array_shift($plugins);
        $hash = sha1(json_encode([
            Plugin::FIELD__STAGE => 'test.install.stage',
            Plugin::FIELD__CLASS => PluginEmpty::class,
            Plugin::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
        ]));
        $this->assertEquals(
            [
                Plugin::FIELD__STAGE => 'test.install.stage',
                Plugin::FIELD__CLASS => PluginEmpty::class,
                Plugin::FIELD__INSTALL_ON => IInitializer::ON__INSTALL,
                Plugin::FIELD__HASH => $hash
            ],
            $plugin->__toArray(),
            'Incorrect plugin: ' . print_r($plugin, true)
        );
    }

    public function testInstallOnePluginForMultipleStages()
    {
        $installer = $this->getInstaller();
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => PluginEmpty::class,
                        Plugin::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);
        $this->reloadSnuffPlugins();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $this->assertEquals(PluginEmpty::class, get_class($plugin));
        }

        foreach(Plugins::byStage('test2.install.stage') as $plugin) {
            $this->assertEquals(PluginEmpty::class, get_class($plugin));
        }
    }

    public function testInstallMultiplePluginForOneStage()
    {
        $installer = $this->getInstaller();
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => [PluginEmpty::class, PluginEmpty::class],
                        Plugin::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);
        $this->reloadSnuffPlugins();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $this->assertEquals(PluginEmpty::class, get_class($plugin));
        }
    }

    public function testInstallMultiplePluginForMultipleStages()
    {
        $installer = $this->getInstaller();
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => [PluginEmpty::class, PluginEmpty::class],
                        Plugin::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);
        $this->reloadSnuffPlugins();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $this->assertEquals(PluginEmpty::class, get_class($plugin));
        }

        foreach(Plugins::byStage('test2.install.stage') as $plugin) {
            $this->assertEquals(PluginEmpty::class, get_class($plugin));
        }
    }

    public function testInstallExtensionsForMultipleSubjects()
    {
        $installer = $this->getInstaller();
        $installer->installPackages([
            [
                'name' => 'test',
                'extensions' => [
                    [
                        Extension::FIELD__SUBJECT => ['test.install.stage', 'test2.install.stage'],
                        Extension::FIELD__CLASS => PluginEmpty::class,
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);

        /**
         * @var IExtension[] $extensions
         */
        $extensions = $this->allSnuffRepos('extRepo', [Extension::FIELD__CLASS => PluginEmpty::class]);
        $this->assertCount(2, $extensions);
    }

    public function testExtensionMethodsUpdate()
    {
        $installer = $this->getInstaller();
        $installer->installPackages([[
            'name' => 'test',
            'extensions' => [
                [
                    Extension::FIELD__CLASS => 'NotExistingClass',
                    Extension::FIELD__INTERFACE => 'NotExistingClass',
                    Extension::FIELD__SUBJECT => '*',
                    Extension::FIELD__METHODS => ['test'],
                    IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                ],
                [
                    Extension::FIELD__CLASS => 'NotExistingClass',
                    Extension::FIELD__INTERFACE => 'NotExistingClass',
                    Extension::FIELD__SUBJECT => '*',
                    Extension::FIELD__METHODS => ['test1'],
                    IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                ]
            ]
        ]]);

        /**
         * @var IExtension[] $extensions
         */
        $extensions = $this->allSnuffRepos('extRepo', [Extension::FIELD__CLASS => 'NotExistingClass']);
        $this->assertCount(1, $extensions);

        $ext = array_shift($extensions);
        $this->assertEquals(['test', 'test1'], $ext->getMethods());
    }

    /**
     * @return IInstaller
     */
    protected function getInstaller(): IInstaller
    {
        return new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => $this->getInput()
        ]);
    }
}
