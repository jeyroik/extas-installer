<?php
namespace extas\interfaces;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Interface IHasInput
 *
 * @package extas\interfaces
 * @author jeyroik@gmail.com
 */
interface IHasInput
{
    public const FIELD__INPUT = 'input';

    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface;
}
