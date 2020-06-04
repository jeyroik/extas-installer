<?php
namespace tests\plugins\install;

use extas\components\plugins\TSnuffPlugins;
use extas\interfaces\packages\IInitializer;
use extas\interfaces\plugins\IPlugin;
use extas\components\console\TSnuffConsole;
use extas\components\plugins\install\InstallApplication;
use extas\components\plugins\PluginRepository;
use extas\components\repositories\TSnuffRepository;
use tests\PluginGenerateData;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

/**
 * Class InstallApplicationTest
 *
 * @package tests\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallApplicationTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepository;
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->registerSnuffRepos(['pluginRepo' => PluginRepository::class]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testInvoke()
    {
        $app = new InstallApplication([
            InstallApplication::FIELD__INPUT => $this->getInput(),
            InstallApplication::FIELD__OUTPUT => $this->getOutput()
        ]);

        $packages = [
            [
                'name' => 'test',
                'plugins' => [
                    [
                        IPlugin::FIELD__CLASS => PluginGenerateData::class,
                        IPlugin::FIELD__STAGE => 'extas.install.package',
                        IInitializer::FIELD__INSTALL_ON => IInitializer::ON__INSTALL
                    ]
                ]
            ]
        ];
        $generatedData = [] ;

        $this->reloadSnuffPlugins();

        $app($packages, $generatedData);

        $this->assertEquals(['test' => 'is ok'], $generatedData);
        $this->assertCount(1, $this->allSnuffRepos('pluginRepo'));
    }
}
