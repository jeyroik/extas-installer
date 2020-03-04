<?php

use \PHPUnit\Framework\TestCase;
use \extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use \extas\interfaces\repositories\IRepository;

/**
 * Class ExporterTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class ExporterTest extends TestCase
{
    /**
     * @var IRepository|null
     */
    protected ?IRepository $pluginRepo = null;

    protected function setUp(): void
    {
        parent::setUp();
        $env = \Dotenv\Dotenv::create(getcwd() . '/tests/');
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

    public function testExportEntities()
    {

        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => 'NotExistingClass',
            Plugin::FIELD__STAGE => 'extas.export.test'
        ]));

        $exporter = new \extas\components\packages\Exporter();
        $this->expectExceptionMessage('Unknown class "NotExistingClass"');
        $this->pluginRepo->reload();
        $exporter->export(['test']);
    }

    public function testExport()
    {
        $this->pluginRepo->reload();
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => 'NotExistingClass',
            Plugin::FIELD__STAGE => 'extas.export'
        ]));

        $exporter = new \extas\components\packages\Exporter();
        $this->expectExceptionMessage('Unknown class "NotExistingClass"');
        $exporter->export();
    }
}
