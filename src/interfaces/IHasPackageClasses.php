<?php
namespace extas\interfaces;

/**
 * Interface IHasPackageClasses
 *
 * @package extas\interfaces
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IHasPackageClasses
{
    public const FIELD__PACKAGE_CLASSES = 'package_class';

    /**
     * @return $this
     */
    public function installPackageClasses();
}
