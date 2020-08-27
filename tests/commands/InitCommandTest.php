<?php
namespace tests\commands;

use extas\interfaces\stages\IStageInitialize;
use extas\interfaces\stages\IStageInitializeItem;
use extas\interfaces\stages\IStageInitializeSection;

use extas\commands\InitCommand;
use extas\components\console\TSnuffConsole;
use extas\components\crawlers\Crawler;
use extas\components\crawlers\CrawlerRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\CrawlerExtas;
use extas\components\packages\entities\EntityRepository;
use extas\components\plugins\Plugin;
use extas\components\plugins\init\Init;
use extas\components\plugins\init\InitItem;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepository;

use Dotenv\Dotenv;
use extas\interfaces\stages\IStageAfterInit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use tests\commands\misc\PluginAfterInit;

use tests\InitSnuffItems;

/**
 * Class InitCommandTest
 *
 * @package tests\commands
 * @author jeyroik <jeyroik@gmail.com>
 */
class InitCommandTest extends TestCase
{
    use TSnuffConsole;
    use TSnuffRepository;
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->registerSnuffRepos([
            'pluginRepo' => PluginRepository::class,
            'entityRepository' => EntityRepository::class,
            'extRepo' => ExtensionRepository::class,
            'crawlerRepository' => CrawlerRepository::class
        ]);
        $this->createWithSnuffRepo('pluginRepo', new Plugin([
            Plugin::FIELD__CLASS => PluginAfterInit::class,
            Plugin::FIELD__STAGE => IStageAfterInit::NAME
        ]));
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testDispatch()
    {
        $command = new InitCommand();
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command->run($this->getInput(), $output);

        $outputText = $output->fetch();

        $this->assertStringContainsString('Installing extensions...', $outputText);
        $this->assertStringContainsString('Installing plugins...', $outputText);
        $this->assertStringContainsString('Copied container lock file to', $outputText);
        $this->assertStringContainsString('after init', $outputText);

        $output = $this->getOutput(true);
        $command->run($this->getInput(['container-rewrite' => false]), $output);
        $outputText = $output->fetch();

        $this->assertStringContainsString(
            'Container lock file already exists and rewrite is restricted.',
            $outputText
        );
    }

    public function testUnknownRepository()
    {
        $this->createWithSnuffRepo('crawlerRepository', new Crawler([
            Crawler::FIELD__CLASS => CrawlerExtas::class,
            Crawler::FIELD__TAGS => ['extas-package']
        ]));
        $this->createSnuffPlugin(Init::class, [IStageInitialize::NAME]);
        $this->createSnuffPlugin(InitSnuffItems::class, [IStageInitializeSection::NAME . '.snuff_items']);
        $this->createSnuffPlugin(InitItem::class, [IStageInitializeItem::NAME]);

        $command = new InitCommand();
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command->run($this->getInput(['package_filename' => 'test.extas.json']), $output);

        $outputText = $output->fetch();
        $this->assertStringContainsString(
            'Missed or unknown class',
            $outputText
        );
    }
}
