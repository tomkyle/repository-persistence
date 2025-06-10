<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Filters;

class CriteriaCheck implements CriteriaCheckInterface
{
    /**
     * An associative array representing the criteria to be used for filtering records.
     * Each key-value pair represents a field and the value that field must match for a record to be considered valid.
     *
     * @var array<string, mixed> Criteria for filtering records.
     */
    public array $criteria = [];


    /**
     * Initializes a new instance of the CriteriaCheck class with an optional criteria array.
     *
     * Each key in the array is a field name, and its value is the required value for that field.
     *
     * @param array<string, mixed> $criteria An associative array of criteria for filtering records.
     */
    public function __construct(array $criteria = [])
    {
        $this->setCriteria($criteria);
    }


    /**
     * @inheritDoc
     */
    public function __invoke(array|object $record): bool
    {
        return $this->accept($record);
    }


    /**
     * @inheritDoc
     */
    public function accept(array|object $record): bool
    {
        if (!is_array($record)) {
            $record = (array) $record;
        }

        foreach ($this->criteria as $key => $value) {
            // Let check fail if any criterion does NOT match the record
            if (!isset($record[$key]) || $record[$key] !== $value) {
                return false;
            }
        }

        // Check succeeds as record matches all criteria
        return true;
    }

    /**
     * @inherit
     */
    public function setCriteria(array $criteria): self
    {
        $this->criteria = $criteria;
        return $this;
    }


}
