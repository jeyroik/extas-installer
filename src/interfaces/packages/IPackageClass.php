<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasClass;
use extas\interfaces\IItem;

/**
 * Interface IPackageClass
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IPackageClass extends IItem, IHasClass
{
    public const SUBJECT = 'extas.package.class';

    public const FIELD__INTERFACE_NAME = 'interface';

    /**
     * @return string
     */
    public function getInterface(): string;
}
