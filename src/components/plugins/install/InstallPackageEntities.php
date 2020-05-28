<?php
namespace extas\components\plugins\install;

use extas\components\packages\PackageEntity;
use extas\components\SystemContainer;
use extas\interfaces\packages\IInstaller;
use extas\interfaces\packages\IPackageEntityRepository;

/**
 * Class InstallPackageEntities
 *
 * @package extas\components\plugins\install
 * @author jeyroik <jeyroik@gmail.com>
 */
class InstallPackageEntities extends AfterInstallItem
{
    /**
     * @param array $item
     * @param IInstaller $installer
     */
    public function __invoke(array $item, IInstaller &$installer): void
    {
        $package = $installer->getPackage();

        $packageEntity = new PackageEntity([
            PackageEntity::FIELD__PACKAGE => $package['name'] ?? 'unknown',
            PackageEntity::FIELD__ENTITY => $this->getSection(),
            PackageEntity::FIELD__QUERY => $this->getPackageEntityQuery($item),
            PackageEntity::FIELD__ID => '@uuid4'
        ]);

        /**
         * @var $repo IPackageEntityRepository
         */
        $repo = SystemContainer::getItem(IPackageEntityRepository::class);
        $repo->create($packageEntity);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function getPackageEntityQuery(array $item)
    {
        return [
            $this->getUid() => $item[$this->getUid()] ?? ''
        ];
    }
}
