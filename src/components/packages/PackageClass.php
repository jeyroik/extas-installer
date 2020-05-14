<?php
namespace extas\components\packages;

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
    public function getClass(): string
    {
        return $this->config[static::FIELD__CLASS_NAME] ?? '';
    }

    /**
     * @param string $interface
     *
     * @return $this
     */
    public function setInterfaceName($interface)
    {
        $this->config[static::FIELD__INTERFACE_NAME] = (string) $interface;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClassName($class)
    {
        $this->config[static::FIELD__CLASS_NAME] = (string) $class;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
