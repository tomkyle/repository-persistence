<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

/**
 * Trait RepositoryTrait
 *
 * Provides implementation for the RepositoryAware interface, allowing classes using this trait
 * to easily manage and access a repository instance. Classes using this trait should implement
 * the RepositoryAware interface to ensure full compatibility.
 */
trait RepositoryTrait
{
    /**
     * @var RepositoryInterface The repository instance.
     */
    public $repository;

    /**
     * Sets the repository instance.
     *
     * @param RepositoryInterface $repository The repository to be used by the class.
     * @return self Returns the instance of the class using this trait for method chaining.
     */
    public function setRepository(RepositoryInterface $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Retrieves the repository instance.
     *
     * @return RepositoryInterface The repository instance.
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
