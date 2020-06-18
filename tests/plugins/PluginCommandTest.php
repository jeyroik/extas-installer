<?php
namespace tests\plugins;

use extas\commands\InstallCommand;
use extas\commands\UninstallCommand;
use extas\components\console\TSnuffConsole;
use extas\components\extensions\ExtensionRepository;
use extas\components\options\CommandOptionRepository;
use extas\components\plugins\PluginCommandInstall;
use extas\components\plugins\PluginCommandUninstall;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * Class PluginCommandTest
 *
 * @package tests\plugins
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginCommandTest extends TestCase
{
    use TSnuffRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->registerSnuffRepos([
            'commandOptionRepository' => CommandOptionRepository::class,
            'pluginRepository' => PluginRepository::class,
            'extensionRepository' => ExtensionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testInstall()
    {
        $plugin = new PluginCommandInstall();
        $this->assertInstanceOf(InstallCommand::class, $plugin());
    }

    public function testUninstall()
    {
        $plugin = new PluginCommandUninstall();
        $this->assertInstanceOf(UninstallCommand::class, $plugin());
    }
}
