<?php
namespace tests\packages;

use extas\interfaces\extensions\IExtension;
use extas\interfaces\packages\IInitializer;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\entities\EntityRepository;
use extas\components\packages\PackageEntityRepository;
use extas\components\plugins\PluginEmpty;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use extas\components\plugins\Plugin;
use extas\components\Plugins;
use extas\components\packages\Installer;

use Symfony\Component\Console\Output\NullOutput;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

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
            'packageEntityRepository' => PackageEntityRepository::class,
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
        $this->deleteSnuffPlugins();
    }

    public function testInstall()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => $this->getInput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => PluginEmpty::class,
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);

        $this->reloadSnuffPlugins();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }

        $this->assertEquals(1, PluginEmpty::$worked);
    }

    public function testInstallOnePluginForMultipleStages()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => $this->getInput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => PluginEmpty::class,
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
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
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => $this->getInput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => [PluginEmpty::class, PluginEmpty::class],
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
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
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => $this->getInput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => [PluginEmpty::class, PluginEmpty::class],
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
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

    public function testExtensionMethodsUpdate()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => $this->getInput()
        ]);
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
}
