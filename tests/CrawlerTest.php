<?php
namespace tests;

use extas\components\plugins\TSnuffPlugins;
use extas\interfaces\samples\parameters\ISampleParameter;
use extas\components\crawlers\Crawler;
use extas\components\packages\CrawlerExtas;

use extas\interfaces\stages\IStageCrawlPackages;
use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use Symfony\Component\Console\Output\NullOutput;
use extas\components\plugins\PluginEmpty;

/**
 * Class CrawlerTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class CrawlerTest extends TestCase
{
    use TSnuffPlugins;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffPlugins();
    }

    public function testCrawlPackages()
    {
        $key = 'test.crawler.test';
        $params = [
            'package_name' => [
                ISampleParameter::FIELD__NAME => 'package_name',
                ISampleParameter::FIELD__VALUE => 'test.extas.json'
            ]
        ];

        $must = [
            $key => json_decode(file_get_contents(__DIR__ . '/test.extas.json'),true)
        ];
        $must[$key][CrawlerExtas::FIELD__SETTINGS] = ['package_name' => 'test.extas.json'];
        $must[$key][CrawlerExtas::FIELD__WORKING_DIRECTORY] = __DIR__;

        $crawler = new CrawlerExtas([
            CrawlerExtas::FIELD__CRAWLER => new Crawler([Crawler::FIELD__PARAMETERS => $params]),
            CrawlerExtas::FIELD__INPUT => null,
            CrawlerExtas::FIELD__OUTPUT => new NullOutput(),
            CrawlerExtas::FIELD__PATH => getcwd() . '/tests'
        ]);
        $this->createSnuffPlugin(PluginEmpty::class, [IStageCrawlPackages::NAME]);
        $this->reloadSnuffPlugins();
        $this->assertEquals($must, $crawler());
        $this->assertEquals(1, PluginEmpty::$worked);
    }
}
