<?php
namespace extas\interfaces\packages\installers;

use extas\interfaces\IHasClass;
use extas\interfaces\IHasDescription;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;

/**
 * Interface IInstallerOption
 *
 * @package extas\interfaces\packages\installers
 * @author jeyroik@gmail.com
 */
interface IInstallerOption extends IItem, IHasName, IHasDescription, IHasClass
{
    const SUBJECT = 'extas.installer.option';

    const FIELD__SHORTCUT = 'shortcut';
    const FIELD__DEFAULT = 'default';
    const FIELD__MODE = 'mode';
    const FIELD__STAGE = 'stage';

    /**
     * @return array
     */
    public function __toInputOption(): array;

    /**
     * @return string
     */
    public function getShortcut(): string;

    /**
     * @return string
     */
    public function getDefault(): string;

    /**
     * @return int
     */
    public function getMode(): int;

    /**
     * @return string
     */
    public function getStage(): string;

    /**
     * @param string $shortcut
     *
     * @return IInstallerOption
     */
    public function setShortcut(string $shortcut): IInstallerOption;

    /**
     * @param string $default
     *
     * @return IInstallerOption
     */
    public function setDefault(string $default): IInstallerOption;

    /**
     * @param int $mode
     *
     * @return IInstallerOption
     */
    public function setMode(int $mode): IInstallerOption;

    /**
     * @param string $stage
     *
     * @return IInstallerOption
     */
    public function setStage(string $stage): IInstallerOption;
}
