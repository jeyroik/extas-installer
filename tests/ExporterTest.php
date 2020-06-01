<?php
namespace tests;

use extas\components\console\TSnuffConsole;
use extas\components\plugins\TSnuffPlugins;
use extas\components\packages\Exporter;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

/**
 * Class ExporterTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class ExporterTest extends TestCase
{
    use TSnuffPlugins;
    use TSnuffConsole;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    /**
     * Clean up
     */
    public function tearDown(): void
    {
        $this->deleteSnuffPlugins();
    }

    public function testExportEntities()
    {
        $this->createPlugin('.test');

        $exporter = new Exporter([
            Exporter::FIELD__INPUT => $this->getInput(),
            Exporter::FIELD__OUTPUT => $this->getOutput()
        ]);
        $this->expectExceptionMessage('Class \'NotExistingClass\' not found');
        $exporter->export(['test']);
    }

    public function testExport()
    {
        $this->createPlugin('');

        $exporter = new Exporter([
            Exporter::FIELD__INPUT => $this->getInput(),
            Exporter::FIELD__OUTPUT => $this->getOutput()
        ]);
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
        $this->createSnuffPlugin('NotExistingClass', ['extas.export' . $stageSuffix]);
        $this->reloadSnuffPlugins();
    }
}
