<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Interface for persistence mechanisms.
 */
interface Persistence
{
    /**
     * Creates a new record.
     *
     * @param array<string,mixed> $data Data to be persisted.
     * @return string|int The ID of the newly created record.
     *
     * @throws \RuntimeException If persistence fails to create the record.
     */
    public function create(array $data): string|int;

    /**
     * Retrieves a record by ID.
     *
     * @param string|int $id The ID of the record to read.
     * @return array<string,mixed> An associative array of the record.
     *
     * @throws \OutOfBoundsException when no record can be found
     */
    public function read(string|int $id): array;

    /**
     * Retrieves all records.
     *
     * @return array<string|int,mixed>[] An array of associative arrays.
     */
    public function readAll(): array;

    /**
     * Updates an existing record.
     *
     * @param array<string,mixed> $data Data to update the record with.
     * @return int The number of affected rows.
     */
    public function update(array $data): int;

    /**
     * Deletes a record by ID.
     *
     * @param string|int $id The ID of the record to delete.
     * @return int The number of deleted rows.
     */
    public function delete(string|int $id): int;
}
