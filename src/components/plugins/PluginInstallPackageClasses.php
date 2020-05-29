<?php
namespace extas\components\plugins;

use extas\components\packages\installers\InstallerOptions;
use extas\components\packages\PackageClass;
use extas\components\packages\PackageClassRepository;
use extas\components\SystemContainer;
use extas\interfaces\IHasClass;
use extas\interfaces\packages\ICrawlerExtas;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\installers\IInstallerStageItem;
use extas\interfaces\packages\installers\IInstallerStageItems;
use extas\interfaces\packages\IPackageClass;
use extas\interfaces\packages\IPackageClassRepository;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PluginInstallPackageClasses
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallPackageClasses
{
    use TInstallMessages;

    protected string $selfSection = 'package_classes';
    protected string $selfName = 'Interface';

    protected IRepository $repo;
    protected OutputInterface $output;

    /**
     * @param array $package
     * @param $output
     */
    public function __invoke(array $package, $output)
    {
        /**
         * @var $repo IRepository
         */
        $this->repo = new PackageClassRepository();
        $this->output = $output;
        $interfaces = $package[$this->selfSection];

        foreach ($interfaces as $interface) {
            $name = $interface[IPackageClass::FIELD__INTERFACE_NAME];

            $existed = $this->repo->one([IPackageClass::FIELD__INTERFACE_NAME => $name]);
            if ($existed && $this->isTheSameClass($existed, $interface)) {
                $this->alreadyInstalled($name, $this->selfName);
            } else {
                $this->install($name, $interface, $existed);
            }
        }
    }

    /**
     * @param IPackageClass $existed
     * @param array $interface
     * @return bool
     */
    protected function isTheSameClass(IPackageClass $existed, array $interface): bool
    {
        return $existed->getClass() == $interface[IPackageClass::FIELD__CLASS_NAME];
    }

    /**
     * @param $output OutputInterface
     */
    public function updateLockFile($output)
    {
        $this->repo->createLockFile();
        SystemContainer::reset();

        $output->writeln(['<info>Classes lock-file updated</info>']);
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @param $uid
     * @param $item
     * @param IPackageClass|null $existed
     */
    public function install($uid, $item, ?IPackageClass $existed): void
    {
        $this->installing($uid, $this->selfName);
        $method = $existed ? 'update' : 'create';
        $itemObj = new PackageClass($item);
        $this->repo->$method($itemObj);
        $this->installed($uid, $this->selfName, $method);
    }
}
