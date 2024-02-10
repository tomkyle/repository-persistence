<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

/**
 * Interface RepositoryAware
 *
 * Extends the RepositoryProvider interface to include a mechanism for setting a repository.
 * This is typically used in scenarios where an object needs to have a repository injected,
 * allowing for flexible data access management and dependency injection.
 */
interface RepositoryAware extends RepositoryProvider
{
    /**
     * Sets the repository for this object.
     *
     * @param RepositoryInterface $repository The repository to be associated with this object.
     * @return self Returns the instance of this object for method chaining.
     */
    public function setRepository(RepositoryInterface $repository): self;
}
