<?php
namespace extas\components\plugins;

use extas\components\packages\PackageClass;
use extas\components\packages\PackageClassRepository;
use extas\components\SystemContainer;
use extas\interfaces\packages\IPackageClass;
use extas\interfaces\packages\IPackageClassRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginInstallPackageClasses
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallPackageClasses extends PluginInstallDefault
{
    protected $selfUID = IPackageClass::FIELD__INTERFACE_NAME;
    protected $selfSection = 'package_classes';
    protected $selfRepositoryClass = IPackageClassRepository::class;
    protected $selfName = 'Interface';
    protected $selfItemClass = PackageClass::class;

    /**
     * @param $output OutputInterface
     */
    public function updateLockFile($output)
    {
        /**
         * todo add createLockFile to the IContainerClassRepository
         * @var $repo IPackageClassRepository|PackageClassRepository
         */
        $repo = SystemContainer::getItem($this->selfRepositoryClass);
        $repo->createLockFile();
        SystemContainer::reset();

        $output->writeln([
            '<info>Classes lock-file updated</info>'
        ]);
    }
}
