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
    /**
     * @return IRepository
     * @throws \Exception
     */
    public function getRepository(): IRepository
    {
        $repoName = $this->config[IHasRepository::FIELD__REPOSITORY] ?? '';

        try {
            return $this->$repoName();
        } catch (\Exception $e) {
            throw new \Exception('Missed item repo ' . $repoName . ' in ' . get_class($this));
        }
    }
}
