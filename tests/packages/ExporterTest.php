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
        $this->pluginRepo->delete([Plugin::FIELD__CLASS => 'NotExistingClass']);
    }

    public function testExportEntities()
    {
        $this->pluginRepo->reload();
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => 'NotExistingClass',
            Plugin::FIELD__STAGE => 'extas.export.test'
        ]));
        $this->stageRepo->create(new Stage([
            Stage::FIELD__NAME => 'extas.export.test',
            Stage::FIELD__HAS_PLUGINS => true
        ]));

        $exporter = new Exporter();
        $this->expectExceptionMessage('Class "NotExistingClass" not found');
        $exporter->export(['test']);
    }

    public function testExport()
    {
        $this->pluginRepo->reload();
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => 'NotExistingClass',
            Plugin::FIELD__STAGE => 'extas.export'
        ]));
        $this->stageRepo->create(new Stage([
            Stage::FIELD__NAME => 'extas.export.test',
            Stage::FIELD__HAS_PLUGINS => true
        ]));

        $exporter = new Exporter();
        $this->expectExceptionMessage('Class "NotExistingClass" not found');
        $exporter->export();
    }
}
