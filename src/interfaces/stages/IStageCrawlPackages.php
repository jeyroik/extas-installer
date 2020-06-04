<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasIO;

/**
 * Interface IStageCrawlPackages
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageCrawlPackages extends IHasIO
{
    public const NAME = 'extas.crawl.packages';

    /**
     * @param array $packages [<package.name:string> => <package:array>, ...]
     */
    public function __invoke(array &$packages): void;
}
