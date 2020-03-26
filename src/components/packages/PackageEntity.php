<?php
namespace extas\components\packages;

use extas\components\Item;
use extas\components\THasId;
use extas\interfaces\packages\IPackageEntity;

/**
 * Class PackageEntity
 *
 * @package extas\components\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class PackageEntity extends Item implements IPackageEntity
{
    use THasId;

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->config[static::FIELD__ENTITY] ?? '';
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        return $this->config[static::FIELD__PACKAGE] ?? '';
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->config[static::FIELD__QUERY] ?? [];
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
