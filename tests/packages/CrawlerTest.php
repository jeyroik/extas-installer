<?php
namespace tests\packages;

use extas\components\crawlers\Crawler;
use extas\interfaces\samples\parameters\ISampleParameter;
use \PHPUnit\Framework\TestCase;
use extas\components\packages\CrawlerExtas;
use Dotenv\Dotenv;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class CrawlerTest
 *
 * @author jeyroik <jeyroik@gmail.com>
 */
class CrawlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testCrawlPackages()
    {
        $key = 'test.crawler.test';
        $params = ['package_name' => 'test.extas.json'];

        $must = [$key => json_decode(file_get_contents(__DIR__ . '/test.extas.json'),true)];
        $must[$key][CrawlerExtas::FIELD__SETTINGS] = $params;
        $must[$key][CrawlerExtas::FIELD__WORKING_DIRECTORY] = __DIR__;

        $crawler = new CrawlerExtas([
            CrawlerExtas::FIELD__CRAWLER => new Crawler([Crawler::FIELD__PARAMETERS => $params]),
            CrawlerExtas::FIELD__INPUT => null,
            CrawlerExtas::FIELD__OUTPUT => new NullOutput(),
            CrawlerExtas::FIELD__PATH => getcwd() . '/tests'
        ]);
        $this->assertEquals($must, $crawler());
    }
}
