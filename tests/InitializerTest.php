<?php
namespace tests\packages;

use extas\components\extensions\Extension;
use extas\components\packages\PackageClassRepository;
use extas\components\plugins\Plugin;
use extas\interfaces\IHasPackageClasses;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\packages\IPackageClass;
use extas\interfaces\repositories\IRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\Initializer;
use extas\components\plugins\PluginRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class InitializerTest
 *
 * @package tests\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class InitializerTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffPlugins;
    use TSnuffRepository;

    protected IRepository $pluginRepo;
    protected IRepository $extRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'pluginRepo' => PluginRepository::class,
            'extRepo' => ExtensionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testInitCoreEntities()
    {
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $init = new Initializer([
            Initializer::FIELD__INPUT => $this->getInput(),
            Initializer::FIELD__OUTPUT => $output
        ]);
        $plugins = include getcwd() . '/tests/pluginsForInit.php';
        $extensions = include getcwd() . '/tests/extensionsForInit.php';
        $init->run(
            [
                [
                    IInitializer::FIELD__PACKAGE_NAME => 'test',
                    IInitializer::FIELD__PLUGINS => $plugins,
                    IInitializer::FIELD__EXTENSIONS => $extensions,
                    IHasPackageClasses::FIELD__PACKAGE_CLASSES => [
                        [
                            IPackageClass::FIELD__CLASS => Extension::class,
                            IPackageClass::FIELD__INTERFACE_NAME => 'test'
                        ],
                        [
                            IPackageClass::FIELD__CLASS => Extension::class,
                            IPackageClass::FIELD__INTERFACE_NAME => 'test'
                        ],
                        [
                            IPackageClass::FIELD__CLASS => Plugin::class,
                            IPackageClass::FIELD__INTERFACE_NAME => 'test'
                        ]
                    ]
                ]
            ]
        );

        $pluginsInstalled = $this->allSnuffRepos('pluginRepo');
        $this->assertCount(6, $pluginsInstalled);

        $extensions = $this->allSnuffRepos('extRepo');
        $this->assertCount(2, $extensions);

        $outputText = $output->fetch();
        $this->assertStringContainsString('Interface "test" installed', $outputText);
        $this->assertStringContainsString('Interface "test" is already installed', $outputText);
        $this->assertStringContainsString('Classes lock-file updated', $outputText);

        $repo = new PackageClassRepository();
        $repo->delete([IPackageClass::FIELD__INTERFACE_NAME => 'test']);
    }
}
