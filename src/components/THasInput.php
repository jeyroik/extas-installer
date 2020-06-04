<?php
namespace extas\components;

use extas\interfaces\IHasInput;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Trait THasInput
 *
 * @property array $config
 *
 * @package extas\components
 * @author jeyroik@gmail.com
 */
trait THasInput
{
    /**
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->config[IHasInput::FIELD__INPUT];
    }
}
