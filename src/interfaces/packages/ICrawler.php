<?php
namespace extas\interfaces\packages;

use extas\commands\InstallCommand;
use extas\interfaces\IItem;

/**
 * Interface ICrawler
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface ICrawler extends IItem
{
    const SUBJECT = 'extas.crawler';
    const STAGE__CRAWL = 'extas.crawl.packages';
    const FIELD__WORKING_DIRECTORY = '__wd__';
    const FIELD__SETTINGS = '__settings__';
    const SETTING__REWRITE_ALLOW = 'rewrite_allow';

    /**
     * @param $path
     * @param $packageName
     *
     * @return array
     */
    public function crawlPackages($path, $packageName = InstallCommand::DEFAULT__PACKAGE_NAME);
}
