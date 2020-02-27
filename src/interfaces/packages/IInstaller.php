<?php
namespace extas\interfaces\packages;

use extas\interfaces\IItem;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IInstaller
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IInstaller extends IItem
{
    public const SUBJECT = 'extas.installer';

    public const FIELD__STAGES = 'stages';
    public const FIELD__PLUGINS = 'plugins';
    public const FIELD__EXTENSIONS = 'extensions';
    public const FIELD__NAME = 'name';
    public const FIELD__SCHEMA = 'schema';
    public const FIELD__REWRITE = 'rewrite';
    public const FIELD__FLUSH = 'flush';
    public const FIELD__SETTINGS = 'installer_settings';
    public const FIELD__INPUT = 'input';

    public const OPTION__MASK = 'mask';
    public const OPTION__MASK__ANY = '*';
    public const DIRECTIVE__GENERATE = '@directive.generate()';

    public const STAGE__INSTALL = 'extas.install';
    public const STAGE__UNINSTALL = 'extas.uninstall';

    /**
     * @param $packageConfigs array
     * @param $output OutputInterface
     *
     * @return bool|string
     */
    public function installMany($packageConfigs, $output);

    /**
     * @param $packageConfig array
     * @param $output OutputInterface
     *
     * @return bool|string
     */
    public function install($packageConfig, $output);

    /**
     * @return array
     */
    public function getPackageConfig();

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
    public function getGeneratedData();

    /**
     * @return null|InputInterface
     */
    public function getInput(): ?InputInterface;

    /**
     * @param $subject string
     *
     * @return bool
     */
    public function isMasked($subject): bool;
}
