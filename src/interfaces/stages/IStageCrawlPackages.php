<?php
namespace extas\interfaces\stages;

/**
 * Interface IStageCrawlPackages
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageCrawlPackages
{
    public const NAME = 'extas.crawl.packages';

    /**
     * @param array $packages [<package.name:string> => <package:array>, ...]
     */
    public function __invoke(array &$packages): void;
}
