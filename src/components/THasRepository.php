<?php
namespace extas\components;

use extas\interfaces\IHasRepository;
use extas\interfaces\repositories\IRepository;

/**
 * Trait THasRepository
 *
 * @property array $config
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasRepository
{
    use THasClassHolder;

    /**
     * @return IRepository
     */
    public function getRepository(): IRepository
    {
        return $this->getClassHolder($this->config[IHasRepository::FIELD__REPOSITORY])->buildClassWithParameters();
    }
}
