<?php
namespace tests\packages;

use Dotenv\Dotenv;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\ExtensionRepositoryGet;
use extas\components\packages\Initializer;
use extas\components\plugins\PluginEmpty;
use extas\components\plugins\PluginRepository;
use extas\interfaces\extensions\IExtension;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\interfaces\repositories\IRepository;
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

    protected IRepository $pluginRepo;
    protected IRepository $extRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->pluginRepo = new PluginRepository();
        $this->extRepo = new ExtensionRepository();
    }

    protected function tearDown(): void
    {
        $this->pluginRepo->delete([IPlugin::FIELD__PRIORITY => -1]);
        $this->extRepo->delete([IExtension::FIELD__CLASS => ExtensionRepositoryGet::class]);
    }

    public function test()
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

        $pluginsInstalled = $this->pluginRepo->all([]);
        $this->assertCount(6, $pluginsInstalled);

        $extensions = $this->extRepo->all([]);
        $this->assertCount(1, $extensions);
    }
}
