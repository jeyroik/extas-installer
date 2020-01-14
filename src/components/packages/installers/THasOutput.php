<?php
namespace extas\components\packages\installers;

use extas\interfaces\packages\installers\IHasOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait THasOutput
 *
 * @property $config
 *
 * @package extas\components\packages\installers
 * @author jeyroik@gmail.com
 */
trait THasOutput
{
    /**
     * @return null|OutputInterface
     */
    public function getOutput(): ?OutputInterface
    {
        return $this->config[IHasOutput::FIELD__OUTPUT] ?? null;
    }
}
