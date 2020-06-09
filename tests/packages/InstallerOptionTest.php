<?php
namespace tests\packages;

use Dotenv\Dotenv;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\ExtensionRepository;
use extas\components\items\SnuffRepository;
use extas\components\packages\Installer;
use extas\components\packages\installers\InstallerOption;
use extas\components\packages\installers\InstallerOptionRepository;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use PHPUnit\Framework\TestCase;

/**
 * Class InstallerOptionTest
 *
 * @package tests\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallerOptionTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepository;
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'tests' => SnuffRepository::class,
            'pluginRepository' => PluginRepository::class,
            'extensionRepository' => ExtensionRepository::class,
            'installerOptionRepository' => InstallerOptionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testChangingAlreadyInstalledEntity()
    {
        $installer = new Installer([
            Installer::FIELD__INPUT => $this->getInput(['test' => true]),
            Installer::FIELD__OUTPUT => $this->getOutput()
        ]);

        $this->createSnuffPlugin(InstallTests::class, ['extas.install']);
        $installer->installMany([ $this->getPackage() ]);

        $this->registerSnuffRepos([
            'tests' => SnuffRepository::class,
            'pluginRepository' => PluginRepository::class,
            'extensionRepository' => ExtensionRepository::class,
            'installerOptionRepository' => InstallerOptionRepository::class
        ]);

        $this->createWithSnuffRepo('installerOptionRepository', new InstallerOption([
            'name' => 'test',
            'title' => 'test',
            'description' => 'test',
            'shortcut' => '',
            'mode' => 4,
            'default' => true,
            'stage' => 'items',
            'class' => ChangeTestEntity::class
        ]));

        $installer->installMany([ $this->getPackage() ]);

        $tests = $this->allSnuffRepos('tests');
        $this->assertCount(1, $tests);
        $test = array_shift($tests);
        $this->assertEquals(
            [
                'name' => 't1',
                'title' => 't1',
                'params' => [
                    'prev_params' => ['name' => 't1'],
                    'new_params' => 'empty'
                ]
            ],
            $test->__toArray(),
            'Mismatched item config: ' . print_r($test->__toArray(), true)
        );
    }

    protected function getPackage(): array
    {
        return [
            'name' => 'test/test',
            'package_classes' => [
                [
                    'interface' => 'tests',
                    'class' => SnuffRepository::class
                ]
            ],
            'tests' => [
                [
                    'name' => 't1',
                    'title' => 't1',
                    'params' => [
                        [
                            'name' => 't1'
                        ]
                    ]
                ]
            ]
        ];
    }
}
