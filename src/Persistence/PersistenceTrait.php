<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Trait for common persistence mechanism handling.
 */
trait PersistenceTrait
{
    /**
     * @var Persistence The persistence mechanism.
     */
    public $persistence;

    /**
     * Retrieves the persistence mechanism.
     *
     * @return Persistence The persistence mechanism.
     */
    public function getPersistence(): Persistence
    {
        return $this->persistence;
    }

    /**
     * Sets the persistence mechanism.
     *
     * @param Persistence $persistence The persistence mechanism.
     * @return self Returns the instance for fluent interface.
     */
    public function setPersistence(Persistence $persistence): self
    {
        $this->persistence = $persistence;
        return $this;
    }
}
