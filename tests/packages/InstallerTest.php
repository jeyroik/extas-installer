<?php
namespace tests\packages;

use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\installers\InstallerOption;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\components\SystemContainer;
use extas\interfaces\extensions\IExtension;
use \PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\Plugin;
use extas\components\Plugins;
use extas\interfaces\repositories\IRepository;
use extas\components\packages\Installer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use tests\InstallerOptionTest;
use tests\NothingRepository;
use tests\PluginInstallNothing;

/**
 * Class InstallerTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallerTest extends TestCase
{
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

        SystemContainer::addItem(NothingRepository::class, NothingRepository::class);
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
    }

    public function testInstall()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput()
        ]);
        $installer->install([
            'name' => 'test',
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

    public function testInstallerOptionsApplying()
    {
        $installer = new Installer([
            Installer::FIELD__OUTPUT => new NullOutput(),
            Installer::FIELD__INPUT => new ArrayInput([
                'test' => true
            ])
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
                    "name" => "test",
                    "value" => "is ok"
                ],
                [
                    "name" => "test1",
                    "value" => "is failed"
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
