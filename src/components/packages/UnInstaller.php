<?php
namespace extas\components\packages;

use extas\components\Item;
use extas\components\packages\entities\EntityRepository;
use extas\components\THasInput;
use extas\components\THasOutput;
use extas\interfaces\packages\entities\IEntity;
use extas\interfaces\packages\entities\IEntityRepository;
use extas\interfaces\packages\IPackageEntity;
use extas\interfaces\packages\IPackageEntityRepository;
use extas\interfaces\packages\IUnInstaller;
use extas\interfaces\repositories\IRepository;

/**
 * Class UnInstaller
 *
 * @package extas\components\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class UnInstaller extends Item implements IUnInstaller
{
    use THasInput;
    use THasOutput;

    protected IPackageEntityRepository $packageEntityRepo;
    protected IEntityRepository $entityRepo;

    /**
     * UnInstaller constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->packageEntityRepo = new PackageEntityRepository();
        $this->entityRepo = new EntityRepository();
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        /**
         * @var $repos IRepository[]
         */
        $repos = [];
        $entitiesByNames = $this->getEntitiesByNames();
        $packageEntities = $this->getPackageEntities($this->buildQuery());

        foreach ($packageEntities as $packageEntity) {
            $entityName = $packageEntity->getEntity();
            if (!isset($entitiesByNames[$entityName])) {
                $this->errorLn(['Unknown entity "' . $entityName . '"']);
                continue;
            }

            if (!isset($repos[$entityName])) {
                $repos[$entityName] = $entitiesByNames[$entityName]->buildClassWithParameters();
            }

            $query = $packageEntity->getQuery();
            $repos[$entityName]->delete($query) && $this->commitUninstall($query, $entityName, $packageEntity);
        }

        return true;
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->config[static::FIELD__PACKAGE] ?? '';
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->config[static::FIELD__ENTITY] ?? '';
    }

    /**
     * @param array $query
     * @param string $entityName
     * @param IPackageEntity $packageEntity
     */
    protected function commitUninstall(array $query, string $entityName, IPackageEntity $packageEntity)
    {
        $this->infoLn(['Uninstalled "' . $entityName . '" described as:']);
        foreach ($query as $field => $value) {
            $this->writeLn([$field . ' = ' . $value]);
        }

        $this->packageEntityRepo->delete('', $packageEntity);

        $stage = 'uninstalled.' . $packageEntity->getPackage();
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            $plugin($packageEntity);
        }

        $stage = 'uninstalled.' . $entityName;
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            $plugin($packageEntity);
        }
    }

    /**
     * @return array
     */
    protected function buildQuery(): array
    {
        $query = [
            IPackageEntity::FIELD__PACKAGE => [],
            IPackageEntity::FIELD__ENTITY => []
        ];

        $packages = $this->getPackageNames();
        foreach ($packages as $package) {
            $query[IPackageEntity::FIELD__PACKAGE][] = $package;
        }

        if (empty($query[IPackageEntity::FIELD__PACKAGE])) {
            unset($query[IPackageEntity::FIELD__PACKAGE]);
        }

        $entities = $this->getEntitiesNames();
        foreach ($entities as $entity) {
            $query[IPackageEntity::FIELD__ENTITY][] = $entity;
        }

        if (empty($query[IPackageEntity::FIELD__ENTITY])) {
            unset($query[IPackageEntity::FIELD__ENTITY]);
        }

        return $query;
    }

    /**
     * @param $query
     * @return IPackageEntity[]
     */
    protected function getPackageEntities($query): array
    {
        return $this->packageEntityRepo->all($query);
    }

    /**
     * @return IEntity[]
     */
    protected function getEntitiesByNames(): array
    {
        /**
         * @var $entities IEntity[]
         * @var $entitiesByNames IEntity[]
         */
        $entities = $this->entityRepo->all([]);
        $entitiesByNames = [];
        foreach ($entities as $entity) {
            $entitiesByNames[$entity->getName()] = $entity;
        }

        return $entitiesByNames;
    }

    /**
     * @return array
     */
    protected function getEntitiesNames(): array
    {
        return $this->convertInput2Names($this->getEntityName());
    }

    /**
     * @return array
     */
    protected function getPackageNames(): array
    {
        return $this->convertInput2Names($this->getPackageName());
    }

    /**
     * @param string $input
     * @return array
     */
    protected function convertInput2Names(string $input): array
    {
        if ($input) {
            $names = explode(',', $input);
            foreach ($names as $index => $name) {
                $names[$index] = trim($name);
            }
            return $names;
        } else {
            return [];
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
