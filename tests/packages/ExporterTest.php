<?php

use PHPUnit\Framework\TestCase;
use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\interfaces\repositories\IRepository;
use extas\components\packages\Exporter;
use Dotenv\Dotenv;

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

    /**
     * @var string
     */
    protected string $currentStage = '';

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

    public function testExportEntities()
    {
        $this->createPlugin('.test');

        $exporter = new Exporter();
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        $exporter->export(['test']);
    }

    public function testExport()
    {
        $this->createPlugin('');

        $exporter = new Exporter();
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        $exporter->export();
    }

    /**
     * Create plugin and stage records.
     *
     * @param string $stageSuffix
     */
    protected function createPlugin(string $stageSuffix)
    {
        $this->pluginRepo->reload();
        $plugin = new Plugin([
            Plugin::FIELD__CLASS => 'NotExistingClass',
            Plugin::FIELD__STAGE => 'extas.export' . $stageSuffix
        ]);
        $this->pluginRepo->create($plugin);
    }
}
