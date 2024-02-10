<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * In-memory implementation of the Persistence interface.
 */
class InMemoryPersistence implements Persistence
{
    /**
     * @var array<array<string,mixed>> The data storage.
     */
    protected array $data = [];


    /**
     * @param array<array<string,mixed>> $records The records to store.
     */
    public function __construct(array $records = [])
    {
        $this->setRecords($records);
    }

    /**
     * Sets the records in the storage.
     *
     * @param array<array<string,mixed>> $records The records to store.
     */
    public function setRecords(array $records): self
    {
        $this->data = $records;
        return $this;
    }

    /**
     * Gets the records from the storage.
     *
     * @return array<array<string,mixed>> The stored records.
     */
    public function getRecords(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): string|int
    {
        $id = array_key_exists('id', $data) ? $data['id'] : uniqid(more_entropy: true);
        if (!is_string($id) && !is_int($id)) {
            throw new \UnexpectedValueException("Expected 'id' to be int or string");
        }

        $this->data[$id] = $data;
        return $id;
    }

    /**
     * @inheritDoc
     */
    public function read(string|int $id): array
    {
        $id .= '';
        if (array_key_exists($id, $this->data)) {
            return $this->data[$id];
        }

        $msg = sprintf("Failed to find record with ID '%s'.", $id);
        throw new \OutOfBoundsException($msg);
    }

    /**
     * @inheritDoc
     */
    public function readAll(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function update(array $data): int
    {
        $id = $data['id'] ?? null;
        if (!is_string($id) && !is_int($id)) {
            $msg = "Element 'id' not of type string|int";
            throw new \UnexpectedValueException($msg);
        }

        $id .= '';
        $this->data[$id] = $data;
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function delete(string|int $id): int
    {
        $id .= '';
        if (!isset($this->data[$id])) {
            return 0;
        }

        unset($this->data[$id]);
        return 1;
    }
}
