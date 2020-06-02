<?php
namespace extas\components\packages;

use extas\components\THasClass;
use extas\interfaces\packages\IPackageClass;
use extas\components\Item;

/**
 * Class PackageClass
 *
 * @package extas\components\classes
 * @author jeyroik@gmail.com
 */
class PackageClass extends Item implements IPackageClass
{
    use THasClass;

    /**
     * @return string
     */
    public function getInterface(): string
    {
        return $this->config[static::FIELD__INTERFACE_NAME] ?? '';
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
