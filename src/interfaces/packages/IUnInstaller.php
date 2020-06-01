<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IItem;

/**
 * Interface IUnInstaller
 *
 * @package extas\interfaces\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IUnInstaller extends IItem, IHasInput, IHasOutput
{
    public const SUBJECT = 'extas.uninstaller';

    public const FIELD__PACKAGE = 'package';
    public const FIELD__ENTITY = 'entity';

    /**
     * @return mixed
     */
    public function uninstall();

    /**
     * @return string
     */
    public function getPackageName(): string;

    /**
     * @return string
     */
    public function getEntityName(): string;
}
