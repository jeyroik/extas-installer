<?php

use PHPUnit\Framework\TestCase;
use extas\components\plugins\Plugin;
use extas\components\plugins\PluginRepository;
use extas\interfaces\repositories\IRepository;
use extas\components\SystemContainer;
use extas\interfaces\stages\IStageRepository;
use extas\components\stages\Stage;
use extas\components\packages\Exporter;

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
     * @var IRepository|null
     */
    protected ?IRepository $stageRepo = null;

    /**
     * @var string
     */
    protected string $currentStage = '';

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
        $this->stageRepo = SystemContainer::getItem(IStageRepository::class);
    }

    /**
     * Clean up
     */
    public function tearDown(): void
    {
        if ($this->currentStage) {
            $this->pluginRepo->delete([Plugin::FIELD__CLASS => 'NotExistingClass']);
            $this->stageRepo->delete([Stage::FIELD__NAME => $this->currentStage]);
        }
    }

    public function testExportEntities()
    {
        $this->createPluginAndStage('.test');

        $exporter = new Exporter();
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        $exporter->export(['test']);
    }

    public function testExport()
    {
        $this->createPluginAndStage('');

        $exporter = new Exporter();
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        $exporter->export();
    }

    /**
     * Create plugin and stage records.
     *
     * @param string $stageSuffix
     */
    protected function createPluginAndStage(string $stageSuffix)
    {
        $this->pluginRepo->reload();
        $plugin = new Plugin([
            Plugin::FIELD__CLASS => 'NotExistingClass',
            Plugin::FIELD__STAGE => 'extas.export' . $stageSuffix
        ]);
        $this->pluginRepo->create($plugin);

        $stage = new Stage([
            Stage::FIELD__NAME => 'extas.export' . $stageSuffix,
            Stage::FIELD__HAS_PLUGINS => true
        ]);
        $this->stageRepo->create($stage);
        $this->currentStage = 'extas.export' . $stageSuffix;
    }
}
