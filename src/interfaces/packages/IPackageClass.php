<?php
namespace extas\interfaces\packages;

use extas\interfaces\IItem;

/**
 * Interface IPackageClass
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IPackageClass extends IItem
{
    const SUBJECT = 'extas.package.class';

    const FIELD__INTERFACE_NAME = 'interface';
    const FIELD__CLASS_NAME = 'class';

    /**
     * @return string
     */
    public function getInterfaceName(): string;

    /**
     * @return string
     */
    public function getClassName(): string;

    /**
     * @param string $interface
     *
     * @return $this
     */
    public function setInterfaceName($interface);

    /**
     * @param string $class
     *
     * @return $this
     */
    public function setClassName($class);
}
