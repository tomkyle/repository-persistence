<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Interface for classes that can have a persistence mechanism set.
 */
interface PersistenceAware extends PersistenceProvider
{
    /**
     * Sets the persistence mechanism.
     *
     * @param Persistence $persistence The persistence mechanism.
     * @return self Returns the instance for fluent interface.
     */
    public function setPersistence(Persistence $persistence): self;
}
