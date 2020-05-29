<?php
namespace extas\interfaces\packages;

use extas\interfaces\IHasExtensions;
use extas\interfaces\IHasInput;
use extas\interfaces\IHasOutput;
use extas\interfaces\IHasPlugins;
use extas\interfaces\IItem;

/**
 * Interface IInstaller
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IInstaller extends IItem, IHasPlugins, IHasExtensions, IHasInput, IHasOutput
{
    public const SUBJECT = 'extas.installer';

    public const FIELD__NAME = 'name';
    public const FIELD__SCHEMA = 'schema';
    public const FIELD__SETTINGS = 'installer_settings';

    /**
     * @param $packages array
     *
     * @return bool
     */
    public function installPackages(array $packages): bool;

    /**
     * @param string $packageName
     * @param array $package
     * @return bool
     */
    public function installPackage(string $packageName, array $package): bool;

    /**
     * @return array
     */
    public function getPackage(): array;

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function addGeneratedData($name, $value);

    /**
     * @return array
     */
    public function getGeneratedData(): array;
}
