<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

use tomkyle\RepositoryPersistence\Persistence\Persistence;

/**
 * Abstract class RepositoryDecoratorAbstract
 *
 * Provides a foundation for repository decorators by implementing the RepositoryInterface
 * and using the RepositoryTrait to comply with the RepositoryAware interface.
 * This class allows for the extension and customization of repository operations.
 */
abstract class RepositoryDecoratorAbstract implements RepositoryInterface, RepositoryAware
{
    use RepositoryTrait;


    /**
     * The Constructor accepts the Repository decoratee.
     *
     * When inheriting from this class and overriding the constructor,
     * do not forget to either call the constructor from the parent
     * (i.e. this) class, or explicitly set the repository with setRepository().
     *
     * @param RepositoryInterface $repository The repository to decorate.
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->setRepository($repository);
    }


    /**
     * Returns the aggregated Repository decoratee.
     *
     * @inheritDoc
     */
    #[\Override]
    public function getPersistence(): Persistence
    {
        return $this->repository->getPersistence();
    }

    /**
     * Sets the inner Repository decoratee.
     *
     * @inheritDoc
     */
    #[\Override]
    public function setPersistence(Persistence $persistence): self
    {
        $this->repository->setPersistence($persistence);
        return $this;
    }


    /**
     * Delegates getNextId() method call to the inner Repository decoratee.
     * {@inheritdoc}
     */
    #[\Override]
    public function getNextId(): int|string
    {
        return $this->repository->getNextId();
    }


    /**
     * Delegates get() method call to the inner Repository decoratee.
     * {@inheritdoc}
     */
    #[\Override]
    public function get($id): object|array
    {
        return $this->repository->get($id);
    }

    /**
     * Delegates findOneBy() method call to the inner Repository decoratee.
     *
     * {@inheritdoc}
     */
    #[\Override]
    public function findOneBy(array $criteria): null|object|array
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Delegates findAll() method call to the inner Repository decoratee.
     *
     * {@inheritdoc}
     */
    #[\Override]
    public function findAll(): iterable
    {
        return $this->repository->findAll();
    }

    /**
     * Delegates findBy() method call to the inner Repository decoratee.
     *
     * {@inheritdoc}
     */
    #[\Override]
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): iterable
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Delegates save() method call to the inner Repository decoratee.
     *
     * {@inheritdoc}
     */
    #[\Override]
    public function save(object|array $entity): bool
    {
        return $this->repository->save($entity);
    }

    /**
     * Delegates delete() method call to the inner Repository decoratee.
     *
     * {@inheritdoc}
     */
    #[\Override]
    public function delete(object|array $entity): bool
    {
        return $this->repository->delete($entity);
    }
}
