<?php
namespace tests\packages;

use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\TSnuffExtensions;
use extas\components\items\SnuffRepository;
use extas\components\packages\entities\EntityRepository;
use extas\components\packages\installers\InstallerOption;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\components\packages\PackageEntityRepository;
use extas\components\SystemContainer;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\packages\entities\IEntityRepository;
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
use tests\InstallerOptionItemsTest;
use tests\InstallerOptionTest;
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

    /**
     * @var IRepository|null
     */
    protected IRepository $pluginRepo;
    protected IRepository $extRepo;
    protected IRepository $optRepository;
    protected IRepository $nothingRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->nothingRepo = new NothingRepository();
        $this->optRepository = new InstallerOptionRepository();
        $this->extRepo = new ExtensionRepository();
        $this->pluginRepo = new class extends PluginRepository {
            public function reload()
            {
                parent::$stagesWithPlugins = [];
            }
        };

        $this->addReposForExt([
            'tests' => SnuffRepository::class,
            INothingRepository::class => NothingRepository::class,
            IPackageEntityRepository::class => PackageEntityRepository::class,
            IEntityRepository::class => EntityRepository::class
        ]);
    }

    /**
     * Clean up
     */
    public function tearDown(): void
    {
        $this->pluginRepo->delete([Plugin::FIELD__CLASS => [
            'NotExistingClass', PluginInstallNothing::class
        ]]);
        $this->extRepo->delete([Extension::FIELD__CLASS => 'NotExistingClass']);
        $this->optRepository->delete([InstallerOption::FIELD__NAME => 'test']);
        $this->nothingRepo->delete(['title' => 'test']);

        $this->pluginRepo->reload();
    }

    public function testInstall()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->install([
            'name' => 'test',
            'package_classes' => [
                [
                    'interface' => 'tests',
                    'class' => SnuffRepository::class
                ]
            ],
            'plugins' => [
                [
                    Plugin::FIELD__STAGE => 'test.install.stage',
                    Plugin::FIELD__CLASS => 'NotExistingClass'
                ]
            ]
        ]);
        $this->pluginRepo->reload();
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }
    }

    public function testInstallMany()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installMany([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => 'NotExistingClass'
                    ]
                ]
            ]
        ]);
        $this->pluginRepo->reload();
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }
    }

    public function testItemsByOptions()
    {
        $this->nothingRepo->create(new Nothing([
            'name' => 'test',
            'title' => 'test'
        ]));
        $this->nothingRepo->create(new Nothing([
            'name' => 'test0',
            'value' => 'is ok 0',
            'title' => 'test'
        ]));
        $this->nothingRepo->create(new Nothing([
            'name' => 'test1',
            'value' => 'is ok 1',
            'title' => 'test'
        ]));
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
            InstallerOption::FIELD__CLASS => InstallerOptionItemsTest::class,
            InstallerOption::FIELD__STAGE => 'items'
        ]));
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__STAGE => 'extas.install',
            Plugin::FIELD__CLASS => PluginInstallNothing::class
        ]));

        $installer->install(['name' => 'test']);
        $nothings = $this->nothingRepo->all([]);
        $this->assertCount(4, $nothings);

        foreach ($nothings as $nothing) {
            $nothing['name'] == 'test' && $this->assertEquals(
                'is ok',
                $nothing['value'],
                print_r($nothing)
            );
            $nothing['name'] == 'test0' && $this->assertEquals(
                'is ok again',
                $nothing['value'],
                print_r($nothing)
            ;
            $nothing['name'] == 'test1' && $this->assertEquals(
                'is ok 1',
                $nothing['value'],
                print_r($nothing)
            );
            $nothing['name'] == 'test2' && $this->assertEquals(
                'is ok again',
                $nothing['value'],
                print_r($nothing)
            );
        }
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
            InstallerOption::FIELD__CLASS => InstallerOptionTest::class,
            InstallerOption::FIELD__STAGE => 'item'
        ]));
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__STAGE => 'extas.install',
            Plugin::FIELD__CLASS => PluginInstallNothing::class
        ]));
        $installer->install([
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
        ]);
        $nothings = $this->nothingRepo->all([]);
        $this->assertCount(1, $nothings);

        $nothing = array_shift($nothings);

        $this->assertEquals('is ok', $nothing['value']);
    }

    public function testInstallOnePluginForMultipleStages()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installMany([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => \tests\TestPlugin::class
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

        $this->pluginRepo->delete([Plugin::FIELD__CLASS => \tests\TestPlugin::class]);
    }

    public function testInstallMultiplePluginForOneStage()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->installMany([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => 'test.install.stage',
                        Plugin::FIELD__CLASS => [\tests\TestPlugin::class, \tests\Test2Plugin::class]
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
        $installer->installMany([
            [
                'name' => 'test',
                'plugins' => [
                    [
                        Plugin::FIELD__STAGE => ['test.install.stage', 'test2.install.stage'],
                        Plugin::FIELD__CLASS => [\tests\TestPlugin::class, \tests\Test2Plugin::class]
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
        $installer->install([
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
        ]);

        /**
         * @var IExtension[] $extensions
         */
        $extensions = $this->extRepo->all([Extension::FIELD__CLASS => 'NotExistingClass']);
        $this->assertCount(1, $extensions);
        $ext = array_shift($extensions);
        $this->assertEquals(['test', 'test1'], $ext->getMethods());
    }
}
