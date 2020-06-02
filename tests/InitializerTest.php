<?php
namespace tests\packages;

use extas\interfaces\packages\IInitializer;
use extas\interfaces\repositories\IRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\Initializer;
use extas\components\plugins\PluginRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

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
        $init = new Initializer([
            Initializer::FIELD__INPUT => $this->getInput(),
            Initializer::FIELD__OUTPUT => $this->getOutput()
        ]);
        $plugins = include getcwd() . '/tests/pluginsForInit.php';
        $extensions = include getcwd() . '/tests/extensionsForInit.php';
        $init->run(
            [
                [
                    IInitializer::FIELD__PACKAGE_NAME => 'test',
                    IInitializer::FIELD__PLUGINS => $plugins,
                    IInitializer::FIELD__EXTENSIONS => $extensions
                ]
            ]
        );

        $pluginsInstalled = $this->allSnuffRepos('pluginRepo');
        $this->assertCount(6, $pluginsInstalled);

        $extensions = $this->allSnuffRepos('extRepo');
        $this->assertCount(2, $extensions);
    }
}
