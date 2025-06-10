<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * A mock persistence class that simulates data persistence operations without actually storing data.
 */
class NoPersistence implements Persistence
{
    /**
     * @var bool Flag to simulate success (true) or failure (false) in persistence operations.
     */
    protected bool $simulate_success = true;


    /**
     * @param bool $success Optional: True to simulate success, false to simulate failure. Defaults to true.
     */
    public function __construct(bool $success = true)
    {
        $this->simulateSuccess($success);
    }

    /**
     * Configures the simulation to either always succeed or always fail.
     *
     * @param bool $success True to simulate success, false to simulate failure.
     */
    public function simulateSuccess(bool $success): self
    {
        $this->simulate_success = $success;
        return $this;
    }

    /**
     * Simulates creating a new record by returning a mock ID.
     *
     * @param array<string,mixed> $data Data intended to be "persisted."
     * @return string|int The mock ID of the newly "created" record.
     */
    public function create(array $data): string|int
    {
        // Simulate successful creation with a mock ID, without storing data
        return uniqid('mockId_', true);
    }

    /**
     * Simulates retrieving a record by its ID, but intentionally ends up
     * throwing \OutOfBoundsException to indicate no data is stored.
     *
     * @inheritDoc
     *
     * @return array<string,mixed> No array will be returned
     * @throws \OutOfBoundsException Always as no data is stored.
     */
    public function read(string|int $id): array
    {
        $msg = sprintf("Intentionally no item stored for ID '%s'.", $id);
        throw new \OutOfBoundsException($msg);
    }

    /**
     * Simulates retrieving all records (but unfortunately there are none).
     *
     * @inheritDoc
     */
    public function readAll(): array
    {
        return [];
    }

    /**
     * Simulates updating an existing record, always indicating no rows affected.
     *
     * @inheritDoc
     *
     * @return int Always returns 0 as no data is stored or updated.
     */
    public function update(array $data): int
    {
        // Simulate update operation
        return $this->simulate_success ? 1 : 0;
    }

    /**
     * Simulates deleting a record by its ID, always indicating no rows deleted.
     *
     * @param string|int $id The ID of the record to "delete."
     * @return int Always returns 0 as no data is stored or deleted.
     */
    public function delete(string|int $id): int
    {
        // Simulate delete operation
        return $this->simulate_success ? 1 : 0;
    }
}
