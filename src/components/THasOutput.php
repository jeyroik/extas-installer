<?php
namespace extas\components;

use extas\interfaces\IHasOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait THasOutput
 *
 * @property OutputInterface[] $config
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
     * @param array $lines
     */
    public function writeLn(array $lines): void
    {
        $this->config[IHasOutput::FIELD__OUTPUT]->writeln($lines);
    }

    /**
     * @param array $lines
     */
    public function commentLn(array $lines): void
    {
        $this->decorateLines($lines, 'comment');
    }

    /**
     * @param array $lines
     */
    public function errorLn(array $lines): void
    {
        $this->decorateLines($lines, 'error');
    }

    /**
     * @param array $lines
     */
    public function infoLn(array $lines): void
    {
        $this->decorateLines($lines, 'info');
    }

    /**
     * @param array $lines
     * @param string $tag
     */
    public function decorateLines(array $lines, string $tag): void
    {
        foreach ($lines as $index => $line) {
            $lines[$index] = sprintf('<%s>%s</%s>', $tag, $line, $tag);
        }

        $this->writeln($lines);
    }
}
