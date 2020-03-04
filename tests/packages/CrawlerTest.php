<?php

use \PHPUnit\Framework\TestCase;
use extas\components\packages\Crawler;

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
        $env = \Dotenv\Dotenv::create(getcwd() . '/tests/');
        $env->load();
    }

    public function testCrawlPackages()
    {
        $must = [
            __DIR__ => json_decode(file_get_contents(__DIR__ . '/test.extas.json'),true)
        ];
        $must[__DIR__][Crawler::FIELD__SETTINGS] = 'test.settings';
        $must[__DIR__][Crawler::FIELD__WORKING_DIRECTORY] = __DIR__;

        $crawler = new Crawler([
            Crawler::FIELD__SETTINGS => 'test.settings'
        ]);
        $this->assertEquals($must, $crawler->crawlPackages(__DIR__, 'test.extas.json'));
    }
}
