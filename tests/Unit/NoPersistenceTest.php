<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\NoPersistence;

/**
 * Tests the NoPersistence class's ability to simulate persistence operations
 * with configurable outcomes.
 */
class NoPersistenceTest extends TestCase
{
    /**
     * Tests the constructor's ability to set success simulation state.
     */
    public function testConstructorSetsSuccessSimulation(): void
    {
        $withPersistenceSuccess = new NoPersistence(success: true);
        $resultSuccess = $withPersistenceSuccess->update([]);
        $this->assertSame(1, $resultSuccess, 'Update operation should simulate success.');

        $noPersistenceFailure = new NoPersistence(success: false);
        $resultFailure = $noPersistenceFailure->update([]);
        $this->assertSame(0, $resultFailure, 'Update operation should simulate failure.');
    }

    /**
     * Tests ID creation
     */
    public function testIdOnCreation(): void
    {
        $sut = new NoPersistence();
        $result = $sut->create(['foo' => 'bar']);
        $this->assertIsString($result);
    }


    /**
     * Tests ID creation
     */
    public function testExceptionOnRead(): void
    {
        $sut = new NoPersistence();

        $this->expectException(\OutOfBoundsException::class);
        $sut->read(1);
    }



    /**
     * Tests ID creation
     */
    public function testEmptyCollection(): void
    {
        $sut = new NoPersistence();
        $collection = $sut->readAll();

        $this->assertEmpty($collection);
    }



    /**
     * Tests that the update method simulates success or failure based on configuration.
     */
    public function testUpdateSimulatesConfiguredOutcome(): void
    {
        $sut = new NoPersistence(success: true);
        // Default should simulate success
        $this->assertSame(1, $sut->update([]), 'Should simulate success by default.');

        // Configure to simulate failure
        $sut->simulateSuccess(success: false);
        $this->assertSame(0, $sut->update([]), 'Should simulate failure when configured.');
    }

    /**
     * Tests that the delete method simulates success or failure based on configuration.
     */
    public function testDeleteSimulatesConfiguredOutcome(): void
    {
        $sut = new NoPersistence(success: true);
        // Default should simulate success
        $this->assertSame(1, $sut->delete('1'), 'Should simulate success by default.');

        // Configure to simulate failure
        $sut->simulateSuccess(success: false);
        $this->assertSame(0, $sut->delete('1'), 'Should simulate failure when configured.');
    }
}
