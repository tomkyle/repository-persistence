<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Interface for classes that provide a persistence mechanism.
 */
interface PersistenceProvider
{
    /**
     * Retrieves the persistence mechanism.
     *
     * @return Persistence The persistence mechanism.
     */
    public function getPersistence(): Persistence;
}
