<?php
namespace extas\components;

use extas\interfaces\IHasOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait THasOutput
 *
 * @property array $config
 *
 * @package extas\components
 * @author jeyroik@gmail.com
 */
trait THasOutput
{
    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->config[IHasOutput::FIELD__OUTPUT];
    }

    /**
     * @param OutputInterface $output
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->config[IHasOutput::FIELD__OUTPUT] = $output;

        return $this;
    }

    /**
     * @param array $lines
     */
    public function writeLn(array $lines): void
    {
        $this->config[IHasOutput::FIELD__OUTPUT]->writeln($lines);
    }
}
