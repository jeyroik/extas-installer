<?php
namespace extas\interfaces\stages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;

/**
 * Interface IStageExport
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageExport extends IHasInput, IHasOutput
{
    public const NAME = 'extas.export';

    /**
     * @param array $result
     */
    public function __invoke(array &$result): void;
}
