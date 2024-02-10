<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Manages a chain of persistence mechanisms, attempting operations across all members
 * to ensure data consistency and integrity.
 *
 * This class implements the Persistence interface, allowing operations to be executed
 * across a series of Persistence instances. It attempts to ensure that all persistence
 * mechanisms are kept in sync by applying create and update operations across all mechanisms,
 * while aggregate results for read and delete operations.
 */
class PersistenceChain implements Persistence
{
    /**
     * An array of persistence mechanisms.
     *
     * @var Persistence[]
     */
    protected array $persistences;

    /**
     * Constructor to initialize the persistence chain with an array of persistence mechanisms.
     *
     * @param Persistence[] $persistences An array of objects implementing the Persistence interface.
     */
    public function __construct(array $persistences)
    {
        $this->setPersistences($persistences);
    }

    /**
     * Sets the persistence mechanisms for the chain.
     *
     * @param Persistence[] $persistences An array of Persistence instances.
     * @return self Returns the instance of PersistenceChain for method chaining.
     */
    public function setPersistences(array $persistences): self
    {
        $this->persistences = $persistences;
        return $this;
    }

    /**
     * Retrieves the list of configured persistence mechanisms.
     *
     * @return Persistence[] The array of configured Persistence instances.
     */
    public function getPersistences(): array
    {
        return $this->persistences;
    }

    /**
     * Adds a persistence mechanism to the chain.
     *
     * @param Persistence $persistence The Persistence instance to add.
     * @return self Returns the instance of PersistenceChain for method chaining.
     */
    public function push(Persistence $persistence): self
    {
        $this->persistences[] = $persistence;
        return $this;
    }

    /**
     * Attempts to create a new record across all persistence mechanisms, returning the ID from the first.
     *
     * This method attempts the create operation across each configured persistence mechanism in sequence,
     * ensuring that all mechanisms have a chance to persist the data. The ID of the first successful operation
     * is returned. If all mechanisms fail, a \RuntimeException is thrown.
     *
     * @param array<string,mixed> $data The data to be persisted.
     * @return string|int The ID of the newly created record from the first successful persistence.
     *
     * @throws \RuntimeException If all persistence mechanisms fail to create the record.
     */
    public function create(array $data): string|int
    {
        $ids = [];
        $exceptions = [];

        foreach ($this->persistences as $persistence) {
            try {
                $ids[] = $persistence->create($data);
            } catch (\Exception $e) {
                $exceptions[] = $e;
            }
        }

        if (count($ids) == 0) {
            throw new \RuntimeException("All persistence mechanisms failed to create record.");
        }

        return $ids[0];
    }

    /**
     * Retrieves a record by ID, attempting each persistence mechanism until a successful retrieval.
     *
     * This method iterates through each persistence mechanism, attempting to read the specified record.
     * The first non-throwing result is returned. If no persistence mechanism can read the record, an OutOfBoundsException will be thrown.
     *
     * @inheritDoc
     */
    public function read(string|int $id): array
    {
        foreach ($this->persistences as $persistence) {
            try {
                return $persistence->read($id);
            } catch (\OutOfBoundsException) {
            } // nothing found

        }

        $msg = sprintf("Failed to find record with ID '%s' in chained persistence mechanisms.", $id);
        throw new \OutOfBoundsException($msg);
    }

    /**
     * Retrieves all records, attempting each persistence mechanism until the retrieved result array is not empty.
     *
     * @inheritDoc
     */
    public function readAll(): array
    {
        foreach ($this->persistences as $persistence) {
            $result = $persistence->readAll();
            if (!empty($result)) {
                return $result;
            }
        }

        return [];
    }

    /**
     * Attempts to update a record across all persistence mechanisms, aggregating the number of affected rows.
     *
     * This method applies the update operation across each configured persistence mechanism in sequence,
     * aggregating the total number of rows affected by the operation. If an update operation fails in all
     * mechanisms, a \RuntimeException may be thrown.
     *
     * @inheritDoc
     *
     * @return int The total number of rows affected across all persistence mechanisms.
     * @throws \RuntimeException If all persistence mechanisms fail to update the record.
     */
    public function update(array $data): int
    {
        $totalAffectedRows = 0;
        $exceptions = [];

        foreach ($this->persistences as $persistence) {
            try {
                $affectedRows = $persistence->update($data);
                $totalAffectedRows += $affectedRows;
            } catch (\Exception $e) {
                $exceptions[] = $e;
            }
        }

        if ($totalAffectedRows == 0 && $exceptions !== []) {
            throw new \RuntimeException("All persistence mechanisms failed to update record.");
        }

        return $totalAffectedRows;
    }

    /**
     * Deletes a record by ID across all persistence mechanisms, aggregating the number of rows deleted.
     *
     * This method attempts the delete operation across each configured persistence mechanism in sequence,
     * aggregating the total number of rows deleted by the operation. The aggregate result is returned.
     *
     * @inheritDoc
     * @return int The total number of rows deleted across all persistence mechanisms.
     */
    public function delete(string|int $id): int
    {
        $totalAffectedRows = 0;

        foreach ($this->persistences as $persistence) {
            $deletedRows = $persistence->delete($id);
            if ($deletedRows > 0) {
                $totalAffectedRows += $deletedRows;
            }
        }

        return $totalAffectedRows;
    }
}
