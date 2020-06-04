<?php
namespace extas\interfaces;

/**
 * Interface IHasIO
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasIO extends IHasInput, IHasOutput
{
    /**
     * @param array $data
     * @return array
     */
    public function getIO(array $data = []): array;
}
