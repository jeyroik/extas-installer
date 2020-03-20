<?php

use \PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\Plugin;
use extas\components\Plugins;
use extas\interfaces\repositories\IRepository;
use extas\components\packages\Installer;
use Symfony\Component\Console\Output\NullOutput;

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
    protected ?IRepository $pluginRepo = null;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->pluginRepo = new class extends PluginRepository {
            public function reload()
            {
                parent::$stagesWithPlugins = [];
            }
        };
    }

    /**
     * Clean up
     */
    public function tearDown(): void
    {
        $this->pluginRepo->delete([Plugin::FIELD__CLASS => 'NotExistingClass']);
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
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        foreach(Plugins::byStage('test.install.stage') as $plugin) {
            $plugin();
        }
    }
}
