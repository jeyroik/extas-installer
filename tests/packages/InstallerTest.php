<?php
namespace tests\packages;

use extas\components\console\TSnuffConsole;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\TSnuffExtensions;
use extas\components\packages\entities\EntityRepository;
use extas\components\packages\installers\InstallerOption;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\components\packages\PackageEntityRepository;
use extas\components\plugins\PluginEmpty;
use extas\components\plugins\TSnuffPlugins;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\packages\entities\IEntityRepository;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\packages\IPackageEntityRepository;
use \PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\Plugin;
use extas\components\Plugins;
use extas\interfaces\repositories\IRepository;
use extas\components\packages\Installer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use tests\INothingRepository;
use tests\InstallerOptionItemsSnuff;
use tests\Nothing;
use tests\NothingRepository;
use tests\PluginInstallNothing;

/**
 * Class InstallerTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallerTest extends TestCase
{
    use TSnuffExtensions;
    use TSnuffPlugins;
    use TSnuffConsole;

    /**
     * @var IRepository|null
     */
    protected IRepository $pluginRepo;
    protected IRepository $extRepo;
    protected IRepository $optRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->optRepository = new InstallerOptionRepository();
        $this->extRepo = new ExtensionRepository();
        $this->pluginRepo = new class extends PluginRepository {
            public function reload()
            {
                parent::$stagesWithPlugins = [];
            }
        };

        $this->addReposForExt([
            'packageEntityRepository' => PackageEntityRepository::class,
            'entityRepository' => EntityRepository::class
        ]);
    }

    /**
     * Clean up
     */
    public function tearDown(): void
    {
        $this->pluginRepo->drop();
        $this->extRepo->drop();
        $this->optRepository->drop();
        $this->pluginRepo->reload();
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
        $this->pluginRepo->reload();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }

        $this->assertEquals(1, PluginEmpty::$worked);
    }

    public function testInstallMany()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
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
        $this->pluginRepo->reload();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }

        $this->assertEquals(2, PluginEmpty::$worked);
    }

    public function testItemsByOptions()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => new ArrayInput([
                '--test' => true
            ], new InputDefinition([
                new InputOption('test')
            ]))
        ]);
        $this->optRepository->create(new InstallerOption([
            InstallerOption::FIELD__NAME => 'test',
            InstallerOption::FIELD__CLASS => InstallerOptionItemsSnuff::class,
            InstallerOption::FIELD__STAGE => 'items'
        ]));
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__STAGE => 'extas.install',
            Plugin::FIELD__CLASS => PluginInstallNothing::class
        ]));

        $installer->installPackages([['name' => 'test']]);
    }

    public function testInstallerOptionsApplying()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => new ArrayInput([
                '--test' => true
            ], new InputDefinition([
                new InputOption('test')
            ]))
        ]);
        $this->optRepository->create(new InstallerOption([
            InstallerOption::FIELD__NAME => 'test',
            InstallerOption::FIELD__CLASS => InstallerOption::class,
            InstallerOption::FIELD__STAGE => 'item'
        ]));
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__STAGE => 'extas.install',
            Plugin::FIELD__CLASS => PluginInstallNothing::class
        ]));
        $installer->installPackages([[
            'name' => 'test',
            'nothings' => [
                [
                    'name' => 'test',
                    'value' => 'is ok',
                    'title' => 'test'
                ],
                [
                    'name' => 'test1',
                    'value' => 'is failed',
                    'title' => 'test'
                ]
            ]
        ]]);


    }

    public function testInstallOnePluginForMultipleStages()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => \tests\TestPlugin::class,
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);
        $this->pluginRepo->reload();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $this->assertEquals(\tests\TestPlugin::class, get_class($plugin));
        }

        foreach(Plugins::byStage('test2.install.stage') as $plugin) {
            $this->assertEquals(\tests\TestPlugin::class, get_class($plugin));
        }
    }

    public function testInstallMultiplePluginForOneStage()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => [\tests\TestPlugin::class, \tests\Test2Plugin::class],
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);
        $this->pluginRepo->reload();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $this->assertTrue(in_array(get_class($plugin), [\tests\TestPlugin::class, \tests\Test2Plugin::class]));
        }

        $this->pluginRepo->delete([Plugin::FIELD__STAGE => 'test.install.stage']);
    }

    public function testInstallMultiplePluginForMultipleStages()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installPackages([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => [\tests\TestPlugin::class, \tests\Test2Plugin::class],
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ]);
        $this->pluginRepo->reload();

        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $this->assertTrue(in_array(get_class($plugin), [\tests\TestPlugin::class, \tests\Test2Plugin::class]));
        }

        foreach(Plugins::byStage('test2.install.stage') as $plugin) {
            $this->assertTrue(in_array(get_class($plugin), [\tests\TestPlugin::class, \tests\Test2Plugin::class]));
        }

        $this->pluginRepo->delete([Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage']]);
    }

    public function testExtensionMethodsUpdate()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installPackages([[
            'name' => 'test',
            'extensions' => [
                [
                    Extension::FIELD__CLASS => 'NotExistingClass',
                    Extension::FIELD__SUBJECT => '*',
                    Extension::FIELD__METHODS => ['test']
                ],
                [
                    Extension::FIELD__CLASS => 'NotExistingClass',
                    Extension::FIELD__SUBJECT => '*',
                    Extension::FIELD__METHODS => ['test1']
                ]
            ]
        ]]);

        /**
         * @var IExtension[] $extensions
         */
        $extensions = $this->extRepo->all([Extension::FIELD__CLASS => 'NotExistingClass']);
        $this->assertCount(1, $extensions);
        $ext = array_shift($extensions);
        $this->assertEquals(['test', 'test1'], $ext->getMethods());
    }
}
