<?php
namespace extas\interfaces\packages;

use extas\interfaces\IItem;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface IInstaller
 *
 * @package extas\interfaces\packages
 * @author jeyroik@gmail.com
 */
interface IInstaller extends IItem
{
    const SUBJECT = 'extas.installer';

    const FIELD__SERVICE_TEMPLATES = 'service_templates';
    const FIELD__STAGES = 'stages';
    const FIELD__PLUGINS = 'plugins';
    const FIELD__EXTENSIONS = 'extensions';
    const FILED__OPTIONS = 'options';
    const FIELD__NAME = 'name';
    const FILED__DESCRIPTION = 'description';
    const FIELD__SCHEMA = 'schema';

    const FIELD__BASE_INTERFACES_PATH = 'base_interfaces_path';

    const OPTION__MASK = 'mask';
    const OPTION__MASK__ANY = '*';
    const DIRECTIVE__GENERATE = '@directive.generate()';

    const STAGE__INSTALL = 'extas.install';
    const STAGE__UNINSTALL = 'extas.uninstall';

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
     * @param $subject string
     *
     * @return bool
     */
    public function isMasked($subject): bool;
}
