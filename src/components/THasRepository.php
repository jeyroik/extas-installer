<?php
namespace extas\components;

use extas\interfaces\IHasClass;
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
    /**
     * @return IRepository
     */
    public function getRepository(): IRepository
    {
        return $this->getRepositoryWrapper()->buildClassWithParameters();
    }

    /**
     * @return Item
     */
    protected function getRepositoryWrapper()
    {
        return new class ([
            IHasClass::FIELD__CLASS => $this->config[IHasRepository::FIELD__REPOSITORY]
        ]) extends Item {
            use THasClass;
            protected function getSubjectForExtension(): string
            {
                return '';
            }
        };
    }
}
