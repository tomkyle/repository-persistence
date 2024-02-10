<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Persistence;

/**
 * Abstract Decorator
 */
abstract class PersistenceDecoratorAbstract implements Persistence, PersistenceAware
{
    use PersistenceTrait;

    public function __construct(Persistence $persistence)
    {
        $this->setPersistence($persistence);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): string|int
    {
        return $this->persistence->create($data);
    }

    /**
     * @inheritDoc
     */
    public function read(string|int $id): array
    {
        return $this->persistence->read($id);
    }

    /**
     * @inheritDoc
     */
    public function readAll(): array
    {
        return $this->persistence->readAll();
    }

    /**
     * @inheritDoc
     */
    public function update(array $data): int
    {
        return $this->persistence->update($data);
    }

    /**
     * @inheritDoc
     */
    public function delete(string|int $id): int
    {
        return $this->persistence->delete($id);
    }



}
