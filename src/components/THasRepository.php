<?php
namespace extas\components;

use extas\components\exceptions\MissedOrUnknown;
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
     * @throws MissedOrUnknown
     */
    public function getRepository(): IRepository
    {
        $repoName = $this->config[IHasRepository::FIELD__REPOSITORY] ?? '';

        try {
            return $this->$repoName();
        } catch (\Exception $e) {
            throw new MissedOrUnknown('item repository ' . $repoName . ' in ' . get_class($this));
        }
    }
}
