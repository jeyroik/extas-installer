<?php
namespace extas\interfaces;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IHasOutput
 *
 * @package extas\interfaces
 * @author jeyroik@gmail.com
 */
interface IHasOutput
{
    public const FIELD__OUTPUT = 'output';

    /**
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface;

    /**
     * @param array $lines
     */
    public function writeLn(array $lines): void;

    /**
     * @param array $lines
     */
    public function commentLn(array $lines): void;

    /**
     * @param array $lines
     */
    public function errorLn(array $lines): void;

    /**
     * @param array $lines
     */
    public function infoLn(array $lines): void;

    /**
     * @param array $lines
     * @param string $tag
     */
    public function decorateLines(array $lines, string $tag): void;
}
