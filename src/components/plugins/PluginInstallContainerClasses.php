<?php
namespace extas\components\plugins;

use df\components\classes\ContainerClassRepository;
use df\interfaces\classes\IContainerClassRepository;
use extas\components\SystemContainer;
use extas\interfaces\packages\IPackageClass;
use extas\interfaces\packages\IPackageClassRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginInstallContainerClasses
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallContainerClasses extends PluginInstallDefault
{
    protected $selfUID = IPackageClass::FIELD__INTERFACE_NAME;
    protected $selfSection = 'container_classes';
    protected $selfRepositoryClass = IPackageClassRepository::class;
    protected $selfName = 'Interface';

    /**
     * @param $output OutputInterface
     */
    public function updateLockFile($output)
    {
        /**
         * todo add createLockFile to the IContainerClassRepository
         * @var $repo IContainerClassRepository|ContainerClassRepository
         */
        $repo = SystemContainer::getItem($this->selfRepositoryClass);
        $repo->createLockFile();
        SystemContainer::reset();

        $output->writeln([
            '<info>Class lock-file updated</info>'
        ]);
    }
}
