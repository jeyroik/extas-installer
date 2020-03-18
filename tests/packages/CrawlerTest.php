<?php

use \PHPUnit\Framework\TestCase;
use extas\components\packages\Crawler;
use Dotenv\Dotenv;

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
        $key = __DIR__ . '/test.extas.json';

        $must = [
            $key => json_decode(file_get_contents(__DIR__ . '/test.extas.json'),true)
        ];
        $must[$key][Crawler::FIELD__SETTINGS] = 'test.settings';
        $must[$key][Crawler::FIELD__WORKING_DIRECTORY] = __DIR__;

        $crawler = new Crawler([
            Crawler::FIELD__SETTINGS => 'test.settings'
        ]);
        $this->assertEquals($must, $crawler->crawlPackages(__DIR__, 'test.extas.json'));
    }
}
