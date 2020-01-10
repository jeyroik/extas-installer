<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\IHasClass;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Interface IInstallerOptions
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerOptions
{
    /**
     * @param string $stage
     * @param null|InputInterface $input
     *
     * @return \Generator|IHasClass
     */
    public static function byStage(string $stage, ?InputInterface $input);
}
