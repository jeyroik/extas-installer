<?php
namespace extas\components;

use extas\components\packages\PackageClass;
use extas\components\packages\PackageClassRepository;
use extas\interfaces\IHasPackageClasses;
use extas\interfaces\packages\IPackageClass;
use extas\interfaces\repositories\IRepository;

/**
 * Trait THasPackageClasses
 *
 * @property array $config
 * @method void writeLn(array $messages)
 *
 * @package extas\components
 * @author jeyroik <jeyroik@gmail.com>
 */
trait THasPackageClasses
{
    protected IRepository $packageClassesRepo;

    /**
     * @return $this
     */
    public function installPackageClasses()
    {
        /**
         * @var $repo IRepository
         */
        $this->packageClassesRepo = new PackageClassRepository();
        $interfaces = $this->config[IHasPackageClasses::FIELD__PACKAGE_CLASSES] ?? [];

        foreach ($interfaces as $interface) {
            $name = $interface[IPackageClass::FIELD__INTERFACE_NAME];

            $existed = $this->packageClassesRepo->one([IPackageClass::FIELD__INTERFACE_NAME => $name]);
            if ($existed && $this->isTheSameClass($existed, $interface)) {
                $this->writeLn([sprintf('Interface "%s" is already installed', $name)]);
            } else {
                $this->install($name, $interface, $existed);
            }
        }

        return $this;
    }

    /**
     * @param IPackageClass $existed
     * @param array $interface
     * @return bool
     */
    protected function isTheSameClass(IPackageClass $existed, array $interface): bool
    {
        return $existed->getClass() == $interface[IPackageClass::FIELD__CLASS];
    }

    public function updateLockFile()
    {
        $this->packageClassesRepo->createLockFile();
        SystemContainer::reset();

        $this->infoLn(['Classes lock-file updated']);
    }

    /**
     * @param $uid
     * @param $item
     * @param IPackageClass|null $existed
     */
    public function install($uid, $item, ?IPackageClass $existed): void
    {
        $method = $existed ? 'update' : 'create';
        $itemObj = new PackageClass($item);
        $this->packageClassesRepo->$method($itemObj);
        $this->infoLn([sprintf('Interface "%s" installed.', $uid)]);
    }
}
