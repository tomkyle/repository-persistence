<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

/**
 * Interface RepositoryProvider
 *
 * Defines a contract for objects that can provide a repository.
 * This is typically used in contexts where an object needs to obtain a repository
 * to perform data access operations without directly managing the repository instantiation.
 */
interface RepositoryProvider
{
    /**
     * Retrieves the repository associated with this provider.
     *
     * @return RepositoryInterface The repository instance.
     */
    public function getRepository(): RepositoryInterface;
}
