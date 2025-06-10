<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tomkyle\RepositoryPersistence\Filters;

interface CriteriaCheckInterface
{
    /**
     * A magic method that enables objects of this class to be called as functions.
     * It delegates the call to the `accept` method, effectively checking if the given record meets the criteria.
     *
     * @param array<string|int, mixed>|object $record The record to check against the set criteria. Can be an array or an object.
     * @return bool Returns true if the record matches all criteria; otherwise, false.
     */
    public function __invoke(array|object $record): bool;

    /**
     * Evaluates the given record against the set criteria to determine if it matches all conditions.
     * If the record is an object, it is converted to an array before comparison.
     *
     * @param array<string|int, mixed>|object $record The record to check, provided as an array or an object.
     * @return bool Returns true if the record matches all criteria; false if it fails any criterion.
     */
    public function accept(array|object $record): bool;


    /**
     * Sets the criteria for this filter.
     * This method allows for the dynamic updating of the filter criteria after the object has been instantiated.
     *
     * @param array<string, mixed> $criteria The new criteria to be applied for filtering records.
     * @return self Returns the instance of this class, allowing for method chaining.
     */
    public function setCriteria(array $criteria): self;


}
