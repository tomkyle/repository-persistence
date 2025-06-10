<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Filters\CriteriaCheck;

class CriteriaCheckTest extends TestCase
{
    /**
     * Tests that the CriteriaCheck object correctly filters an array based on the provided criteria.
     * It verifies both scenarios where the array matches and does not match the criteria.
     */
    public function testConstructAndAcceptWithArray()
    {
        $criteria = ['key1' => 'value1', 'key2' => 'value2'];
        $sut = new CriteriaCheck($criteria);

        // Assert that the object filters an array matching the criteria
        $this->assertTrue($sut->accept(['key1' => 'value1', 'key2' => 'value2']));

        // Assert that the object filters out an array not matching the criteria
        $this->assertFalse($sut->accept(['key1' => 'value1']));
    }

    /**
     * Tests that the CriteriaCheck object correctly filters an object based on the provided criteria.
     * It checks both cases where the object's properties match and do not match the criteria.
     */
    public function testAcceptWithObject()
    {
        $criteria = ['key1' => 'value1'];
        $sut = new CriteriaCheck($criteria);

        $record = new \stdClass();
        $record->key1 = 'value1';

        // Assert that the object correctly filters an object matching the criteria
        $this->assertTrue($sut->accept($record));

        $record->key1 = 'wrongValue';
        // Assert that the object filters out an object not matching the criteria
        $this->assertFalse($sut->accept($record));
    }

    /**
     * Tests the __invoke method of the CriteriaCheck object.
     * It validates that the object behaves as expected when used as a callable,
     * filtering records based on matching or non-matching criteria.
     */
    public function testInvokeMethod()
    {
        $criteria = ['key' => 'value'];
        $sut = new CriteriaCheck($criteria);

        // Assert __invoke works with matching criteria
        $this->assertTrue($sut(['key' => 'value']));

        // Assert __invoke works with non-matching criteria
        $this->assertFalse($sut(['key' => 'otherValue']));
    }

    /**
     * Tests the setCriteria method of the CriteriaCheck object.
     * It ensures that the criteria can be updated after instantiation and affects the acceptance of records.
     */
    public function testSetCriteria()
    {
        $sut = new CriteriaCheck();

        $some_criteria = ['name' => 'Alice'];
        $sut->setCriteria($some_criteria);
        $this->assertEquals($some_criteria, $sut->criteria);

        // Set new criteria and test acceptance
        $newCriteria = ['newKey' => 'newValue'];
        $sut->setCriteria($newCriteria);
        $this->assertTrue($sut->accept($newCriteria));
        $this->assertFalse($sut->accept(['newKey' => 'wrongValue']));
    }

    /**
     * Specifically tests that the setCriteria method updates the internal criteria array.
     * Ensures that the criteria set via setCriteria are exactly what the object uses for filtering.
     */
    public function testSetCriteriaUpdatesCriteria(): void
    {
        $criteriaCheck = new CriteriaCheck();
        $criteriaCheck->setCriteria(['name' => 'Alice']);
        $this->assertEquals(['name' => 'Alice'], $criteriaCheck->criteria);
    }
}
