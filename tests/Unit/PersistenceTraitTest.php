<?php

/**
 * tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\Persistence;
use tomkyle\RepositoryPersistence\Persistence\PersistenceTrait;

/**
 * Unit tests for the PersistenceTrait.
 *
 * Verifies that classes using the PersistenceTrait can correctly set and get
 * a persistence mechanism.
 */
class PersistenceTraitTest extends TestCase
{
    /**
     * Test subject using PersistenceTrait in an anonymous class.
     *
     * @var object
     */
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the PersistenceTrait
        $this->sut = new class () {
            use PersistenceTrait;
        };
    }

    /**
     * Tests if the persistence mechanism can be set and retrieved correctly.
     */
    public function testSetAndGetPersistence(): void
    {
        $persistenceMock = $this->createMock(Persistence::class);

        // Set the persistence mechanism
        $this->sut->setPersistence($persistenceMock);

        // Assert that the set persistence mechanism can be retrieved correctly
        $this->assertSame($persistenceMock, $this->sut->getPersistence(), 'The set and retrieved persistence mechanisms should be the same.');
    }
}
