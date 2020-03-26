<?php
namespace extas\components\packages;

use extas\components\Item;
use extas\components\SystemContainer;
use extas\interfaces\packages\entities\IEntity;
use extas\interfaces\packages\entities\IEntityRepository;
use extas\interfaces\packages\IPackageEntity;
use extas\interfaces\packages\IPackageEntityRepository;
use extas\interfaces\packages\IUnInstaller;
use extas\interfaces\repositories\IRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UnInstaller
 *
 * @package extas\components\packages
 * @author jeyroik <jeyroik@gmail.com>
 */
class UnInstaller extends Item implements IUnInstaller
{
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
                $this->output(['<error>Unknown entity "' . $entityName . '"</error>']);
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
     * @return InputInterface|null
     */
    public function getInput(): ?InputInterface
    {
        return $this->config[static::FIELD__INPUT] ?? null;
    }

    /**
     * @return OutputInterface|null
     */
    public function getOutput(): ?OutputInterface
    {
        return $this->config[static::FIELD__OUTPUT] ?? null;
    }

    /**
     * @param array $query
     * @param string $entityName
     * @param IPackageEntity $packageEntity
     */
    protected function commitUninstall(array $query, string $entityName, IPackageEntity $packageEntity)
    {
        $this->output(['<info>Uninstalled "' . $entityName . '" described as:</info>']);
        foreach ($query as $field => $value) {
            $this->output([$field . ' = ' . $value]);
        }

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
        /**
         * @var $repo IPackageEntityRepository
         */
        $repo = SystemContainer::getItem(IPackageEntityRepository::class);
        return $repo->all($query);
    }

    /**
     * @return IEntity[]
     */
    protected function getEntitiesByNames(): array
    {
        /**
         * @var $entityRepo IEntityRepository
         * @var $entities IEntity[]
         * @var $entitiesByNames IEntity[]
         */
        $entityRepo = SystemContainer::getItem(IEntityRepository::class);
        $entities = $entityRepo->all([]);
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
            return ['*'];
        }
    }

    /**
     * @param $messages
     */
    protected function output($messages)
    {
        $this->config[static::FIELD__OUTPUT]->writeln($messages);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
