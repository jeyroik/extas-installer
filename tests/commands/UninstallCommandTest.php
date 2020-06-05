<?php
namespace tests;

use extas\components\plugins\uninstall\UninstallCrawlers;
use extas\interfaces\stages\IStageUninstall;
use extas\interfaces\stages\IStageUninstallItem;
use extas\interfaces\stages\IStageUninstallPackage;
use extas\interfaces\stages\IStageUninstallSection;
use extas\components\crawlers\Crawler;
use extas\components\options\CommandOptionRepository;
use extas\components\packages\CrawlerExtas;
use extas\commands\UninstallCommand;
use extas\components\console\TSnuffConsole;
use extas\components\crawlers\CrawlerRepository;
use extas\components\extensions\ExtensionRepository;
use extas\components\packages\entities\EntityRepository;
use extas\components\plugins\PluginRepository;
use extas\components\plugins\TSnuffPlugins;
use extas\components\plugins\uninstall\UninstallApplication;
use extas\components\plugins\uninstall\UninstallExtensions;
use extas\components\plugins\uninstall\UninstallItem;
use extas\components\plugins\uninstall\UninstallPackage;
use extas\components\plugins\uninstall\UninstallPlugins;
use extas\components\repositories\TSnuffRepository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class UninstallCommandTest
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class UninstallCommandTest extends TestCase
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
            'pluginRepository' => PluginRepository::class,
            'entityRepository' => EntityRepository::class,
            'extensionRepository' => ExtensionRepository::class,
            'crawlerRepository' => CrawlerRepository::class,
            'commandOptionRepository' => CommandOptionRepository::class
        ]);
    }

    protected function tearDown(): void
    {
        $this->unregisterSnuffRepos();
    }

    public function testWithoutArguments()
    {
        $this->installPlugins();
        $command = new UninstallCommand();
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command->run($this->getInput(), $output);
        $text = $output->fetch();

        $this->assertStringContainsString('Uninstalled package extas/installer', $text, $text);
        $this->assertStringContainsString('Uninstalled package extas/crawlers', $text, $text);
        $this->assertStringContainsString('Uninstalled section plugins', $text, $text);
        $this->assertStringContainsString('Uninstalled section extensions', $text, $text);
    }

    public function testWithPackageArgument()
    {
        $this->installPlugins();
        $command = new UninstallCommand();
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command->run($this->getInput(['package' => 'extas/installer']), $output);
        $text = $output->fetch();
        $this->assertStringContainsString('Uninstalled package extas/installer', $text, $text);
        $this->assertStringNotContainsString('Uninstalled package extas/crawlers', $text, $text);
    }

    public function testWithSectionArgument()
    {
        $this->installPlugins();
        $command = new UninstallCommand();
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command->run($this->getInput(['section' => 'plugins']), $output);
        $text = $output->fetch();

        $this->assertStringContainsString('Uninstalled package extas/installer', $text, $text);
        $this->assertStringContainsString('Uninstalled package extas/crawlers', $text, $text);
        $this->assertStringContainsString('Uninstalled section plugins', $text, $text);
        $this->assertStringNotContainsString('Uninstalled section extensions', $text, $text);
    }

    public function testWithPackageAndSectionArgument()
    {
        $this->installPlugins();
        $command = new UninstallCommand();
        /**
         * @var BufferedOutput $output
         */
        $output = $this->getOutput(true);
        $command->run($this->getInput(['package' => 'extas/installer', 'section' => 'plugins']), $output);
        $text = $output->fetch();
        $this->assertStringContainsString('Uninstalled package extas/installer', $text, $text);
        $this->assertStringNotContainsString('Uninstalled package extas/crawlers', $text, $text);
        $this->assertStringContainsString('Uninstalled section plugins', $text, $text);
        $this->assertStringNotContainsString('Uninstalled section extensions', $text, $text);
    }

    protected function installPlugins()
    {
        $this->createWithSnuffRepo('crawlerRepository', new Crawler([
            Crawler::FIELD__CLASS => CrawlerExtas::class,
            Crawler::FIELD__TAGS => ['extas-package']
        ]));
        $this->createSnuffPlugin(UninstallApplication::class, [IStageUninstall::NAME]);
        $this->createSnuffPlugin(UninstallPackage::class, [IStageUninstallPackage::NAME . '.extas/installer']);
        $this->createSnuffPlugin(UninstallCrawlers::class, [IStageUninstallSection::NAME . '.crawlers']);
        $this->createSnuffPlugin(UninstallPlugins::class, [IStageUninstallSection::NAME . '.plugins']);
        $this->createSnuffPlugin(UninstallExtensions::class, [IStageUninstallSection::NAME . '.extensions']);
        $this->createSnuffPlugin(UninstallItem::class, [IStageUninstallItem::NAME]);
    }
}
