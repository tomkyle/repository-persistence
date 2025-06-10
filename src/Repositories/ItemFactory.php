<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

/**
 * Pass-through ItemFactory
 *
 * Callable pass-through factory intended to be used within a Repository. It just delivers what
 * the aggregated Persistence returned. To create custom objects from certain class,
 * replace with your own callable factory implementation.
 */
class ItemFactory
{
    /**
     * @param array<string,mixed> $item An associative resource data array.
     * @return object|array<string,mixed> Factoried entity object or array.
     */
    public function __invoke(array $item): array|object
    {
        return $item;
    }
}
