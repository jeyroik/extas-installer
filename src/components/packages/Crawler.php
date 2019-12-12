<?php
namespace extas\components\packages;

use extas\commands\InstallCommand;
use extas\interfaces\packages\ICrawler;
use extas\components\Item;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Crawler
 *
 * @package extas\components\packages
 * @author jeyroik@gmail.com
 */
class Crawler extends Item implements ICrawler
{
    /**
     * @param $path
     * @param $packageName
     *
     * @return array
     */
    public function crawlPackages($path, $packageName = InstallCommand::DEFAULT__PACKAGE_NAME)
    {
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
                $config[static::FIELD__SETTINGS] = $this->config[static::FIELD__SETTINGS] ?? [];
            } catch (\Exception $e) {
                continue;
            }
            $extasPackages[$file->getRealPath()] = $config;
        }

        foreach ($this->getPluginsByStage(static::STAGE__CRAWL) as $plugin) {
            $plugin($extasPackages, $path);
        }

        return $extasPackages;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
