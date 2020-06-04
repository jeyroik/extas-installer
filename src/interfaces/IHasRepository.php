<?php
namespace extas\interfaces;

use extas\interfaces\repositories\IRepository;

/**
 * Interface IHasRepository
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasRepository
{
    public const FIELD__REPOSITORY = 'repository';

    /**
     * @return IRepository
     */
    public function getRepository(): IRepository;
}
