<?php
namespace extas\interfaces\packages\installers;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IHasOutput
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IHasOutput
{
    public const FIELD__OUTPUT = 'output';

    /**
     * @return null|OutputInterface
     */
    public function getOutput(): ?OutputInterface;
}
