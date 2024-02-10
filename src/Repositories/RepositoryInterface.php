<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Repositories;

use tomkyle\RepositoryPersistence\Persistence\PersistenceAware;
use App\Resources\ResourceCollectionInterface;

/**
 * Interface RepositoryInterface
 *
 * Defines the standard functions to be implemented by repository classes for CRUD (Create, Read, Update, Delete) operations.
 * It ensures that implementing classes provide methods for finding, saving, and deleting entities within a repository.
 */
interface RepositoryInterface extends PersistenceAware
{
    /**
     * Creates next ID for a new entity.
     * @return int|string ID string or PK integer
     */
    public function getNextId(): int|string;

    /**
     * Returns an entity by its primary key / identifier.
     *
     * @param string|int $id The identifier of the entity.
     * @return object|array<string,mixed> The entity object.
     *
     * @throws \OutOfBoundsException when no record can be found
     */
    public function get($id): array|object;

    /**
     * Finds a single entity based on a set of criteria.
     *
     * Can return a single entity object or null if not found.
     *
     * @param array<string,mixed> $criteria Conditions used for finding the entity.
     * @return null|object|array<string,mixed> The entity object, or null, if none found.
     */
    public function findOneBy(array $criteria): null|array|object;

    /**
     * Finds all entities in the repository.
     *
     * Returns an iterable collection of entity objects. The collection may be empty.
     *
     * @return iterable<string|int,object|array<string,mixed>> An iterable collection of entity objects.
     */
    public function findAll(): iterable;

    /**
     * Finds entities based on a set of criteria.
     *
     * Returns an iterable collection of entity objects, which can be ordered and limited. The collection may be empty.
     *
     * @param array<string, mixed> $criteria Conditions used for finding entities.
     * @param array<string,string>|null $orderBy Order in which to return the results, or null for no specific order.
     * @param int|null $limit Maximum number of entities to return, or null for no limit.
     * @param int|null $offset Offset from which to start the listing, or null to start from the beginning.
     *
     * @return iterable<string|int,object|array<string,mixed>>  An iterable collection of entity objects.
     *
     * @throws \OutOfBoundsException When the method is not supported.
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): iterable;

    /**
     * Saves an entity to the repository.
     *
     * Updates or persists the given entity, depending on if an array key or member variable 'id' exists.
     *
     * @param object|array<string,mixed> $entity The entity to save.
     */
    public function save(object|array $entity): bool;

    /**
     * Deletes an entity from the repository.
     *
     * Removes the specified entity from storage.
     *
     * @param object|array<string,mixed> $entity The entity to delete.
     */
    public function delete(object|array $entity): bool;
}
