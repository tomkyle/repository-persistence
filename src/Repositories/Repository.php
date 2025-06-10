<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

use tomkyle\RepositoryPersistence\Persistence\Persistence;
use tomkyle\RepositoryPersistence\Persistence\PersistenceTrait;
use tomkyle\RepositoryPersistence\Filters\CriteriaCheckInterface;
use tomkyle\RepositoryPersistence\Filters\ArrayCriteriaFilter;
use tomkyle\RepositoryPersistence\Filters\CriteriaCheck;
use Nette\Utils\Iterables;
use Nette\Utils\Random;

/**
 * Repository class for handling data operations.
 *
 * This class provides concrete implementations for the RepositoryInterface, utilizing a persistence
 * layer for data storage and retrieval, and callable factories for item and collection instantiation.
 */
class Repository implements RepositoryInterface
{
    use PersistenceTrait;

    /**
     * Factory callable for creating a single item/entity.
     *
     * @var callable
     */
    protected $item_factory;

    /**
     * Factory callable for creating a collection of items/entities.
     *
     * @var callable
     */
    protected $collection_factory;


    /**
     * @var CriteriaCheckInterface
     */
    protected $check_criteria;

    /**
     * Initializes the repository with persistence, and, optionally, custom item and collection factories.
     *
     * @param Persistence $persistence The persistence mechanism for data storage and retrieval.
     * @param callable|null $item_factory A callable that transforms data into an array|object. Defaults to null.
     * @param callable|null $collection_factory A callable that transforms data into an iterable collection of arrays or objects. Defaults to null.
     */
    public function __construct(Persistence $persistence, ?callable $item_factory = null, ?callable $collection_factory = null, ?CriteriaCheckInterface $criteria_check = null)
    {
        $this->setPersistence($persistence);
        $this->setItemFactory($item_factory ?: new ItemFactory());
        $this->setCollectionFactory($collection_factory ?: new ItemsCollectionFactory());

        $this->setCriteriaCheck($criteria_check ?: new CriteriaCheck());
    }


    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getNextId(): int|string
    {
        return Random::generate(10);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function get($id): array|object
    {
        $item = $this->persistence->read($id);
        return ($this->item_factory)($item);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function findOneBy(array $criteria): null|array|object
    {
        $all = $this->findBy($criteria);
        return (empty($all)) ? null : Iterables::first($all);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function findAll(): iterable
    {
        $items = array_map(fn($item) => ($this->item_factory)($item), $this->persistence->readAll());

        return ($this->collection_factory)($items);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): iterable
    {
        $records = $this->findAll();

        $this->check_criteria->setCriteria($criteria);

        return is_array($records)
        ? (array_filter($records, $this->check_criteria))
        : new \CallbackFilterIterator($records, $this->check_criteria);
    }

    /**
     * Saves or updates an entity in the repository,
     * depending in if the entity has an 'id' key or member.
     *
     * {@inheritdoc}
     */
    #[\Override]
    public function save(array|object $entity): bool
    {
        $entity_array = (array) $entity;
        $id = $this->getEntityId($entity);


        // No ID? Then create one and pass over to persistence.
        if (is_null($id)) {
            $entity_array['id'] = $this->getNextId();
            $result = $this->persistence->create($entity_array);
            return !empty($result);
        }

        // So it has an ID. But it may stem from Controller which consumes this class.
        // Decision if to update or to create now based on wether the entity exists.
        try {
            $this->get($id);
            $result = $this->persistence->update($entity_array);
        } catch (\OutOfBoundsException) {
            $result = $this->persistence->create($entity_array);
        }

        return !empty($result);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function delete(array|object $entity): bool
    {
        $id = $this->getEntityId($entity);

        $result = (is_null($id)) ? null : $this->persistence->delete($id);

        return !empty($result);
    }


    /**
     * @param object|array<string,mixed> $entity The entity
     * @return null|int|string Entity ID, or null if missing.
     */
    protected function getEntityId(array|object $entity): null|int|string
    {
        $entity_array = (array) $entity;
        return $entity_array['id'] ?? null;
    }




    /**
     * Sets the callable for filtering records.
     *
     * @param CriteriaCheckInterface $criteria_filter Criteria check
     */
    public function setCriteriaCheck(CriteriaCheckInterface $criteria_filter): self
    {
        $this->check_criteria = $criteria_filter;
        return $this;
    }


    /**
     * Sets the item factory callable.
     *
     * @param callable $item_factory The callable used to create an item/entity.
     * @return self Chainable method for fluent interfaces.
     */
    public function setItemFactory(callable $item_factory): self
    {
        $this->item_factory = $item_factory;
        return $this;
    }

    /**
     * Sets the collection factory callable.
     *
     * @param callable $collection_factory The callable used to create a collection of items/entities.
     * @return self Chainable method for fluent interfaces.
     */
    public function setCollectionFactory(callable $collection_factory): self
    {
        $this->collection_factory = $collection_factory;
        return $this;
    }
}
