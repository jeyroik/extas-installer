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
    public const SUBJECT = 'extas.crawler';
    public const STAGE__CRAWL = 'extas.crawl.packages';
    public const FIELD__WORKING_DIRECTORY = '__wd__';
    public const FIELD__SETTINGS = '__settings__';
    public const SETTING__REWRITE_ALLOW = 'rewrite_allow';

    /**
     * @param $path
     * @param $packageName
     *
     * @return array
     */
    public function crawlPackages($path, $packageName = InstallCommand::DEFAULT__PACKAGE_NAME);
}
