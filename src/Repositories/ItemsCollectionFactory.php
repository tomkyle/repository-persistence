<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

/**
 * Pass-through ItemsCollectionFactory
 *
 * Callable pass-through factory intended to be used within a Repository. It just delivers what
 * the aggregated Persistence returned. To create objects from certain class,
 * replace this instance with your own callable factory implementation.
 */
class ItemsCollectionFactory
{
    /**
     * @param array<string|int,mixed>[] $items An array of associative arrays.
     * @return iterable<string|int,object|array<string|int,mixed>> An iterable collection of entity resources.
     */
    public function __invoke(array $items): iterable
    {
        return $items;
    }
}
