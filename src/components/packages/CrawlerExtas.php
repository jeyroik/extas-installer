<?php
namespace extas\components\packages;

use extas\commands\InstallCommand;
use extas\components\crawlers\CrawlerDispatcher;
use extas\components\THasIO;
use extas\interfaces\crawlers\ICrawlerDispatcher;
use extas\interfaces\IHasName;
use extas\interfaces\packages\ICrawlerExtas;
use extas\interfaces\stages\IStageCrawlPackages;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class CrawlerExtas
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class CrawlerExtas extends CrawlerDispatcher implements ICrawlerDispatcher, ICrawlerExtas
{
    use THasIO;

    /**
     * @return array
     * @throws \Exception
     */
    public function __invoke(): array
    {
        $crawler = $this->getCrawler();
        $path = $this->getPath();
        $packageName = $crawler->getParameterValue(
            'package_name',
            InstallCommand::DEFAULT__PACKAGE_NAME
        );

        $finder = new Finder();
        $finder->name($packageName);
        $extasPackages = [];

        foreach ($finder->in($path)->files() as $file) {
            /**
             * @var $file SplFileInfo
             */
            try {
                $config = json_decode($file->getContents(), true);
                $config[static::FIELD__WORKING_DIRECTORY] = $file->getPathInfo()->getPathname();
                $config[static::FIELD__SETTINGS] = $crawler->getParametersValues();
            } catch (\Exception $e) {
                continue;
            }
            $currentName = $config[IHasName::FIELD__NAME] ?? $file->getRealPath();
            $extasPackages[$currentName] = $config;
        }

        if ($crawler->getParameterValue('run_after', true)) {
            $this->runCrawlStage($extasPackages);
        }

        return $extasPackages;
    }

    /**
     * @param array $packages
     */
    protected function runCrawlStage(array &$packages): void
    {
        foreach ($this->getPluginsByStage(IStageCrawlPackages::NAME, $this->getIO()) as $plugin) {
            /**
             * @var IStageCrawlPackages $plugin
             */
            $plugin($packages);
        }
    }
}
