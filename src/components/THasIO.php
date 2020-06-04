<?php
namespace extas\components;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;

/**
 * Trait THasIO
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasIO
{
    use THasInput;
    use THasOutput;

    /**
     * @param array $data
     * @return array
     */
    public function getIO(array $data = []): array
    {
        $data[IHasInput::FIELD__INPUT] = $this->getInput();
        $data[IHasOutput::FIELD__OUTPUT] = $this->getOutput();

        return $data;
    }
}
