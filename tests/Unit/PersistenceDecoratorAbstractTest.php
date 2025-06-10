<?php

/**
 * This file is part of tomkyle/repository-persistence
 *
 * Scaffold for Repository-and-Persistence design pattern
 */

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use tomkyle\RepositoryPersistence\Persistence\Persistence;
use tomkyle\RepositoryPersistence\Persistence\PersistenceDecoratorAbstract;

class PersistenceDecoratorAbstractTest extends TestCase
{
    private Persistence $persistenceMock;

    private PersistenceDecoratorAbstract $sut;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        // Create a mock for the Persistence interface.
        $this->persistenceMock = $this->createMock(Persistence::class);
        // Initialize the Subject Under Test (SUT) with an anonymous class extending the abstract class.
        $this->sut = new class ($this->persistenceMock) extends PersistenceDecoratorAbstract {
            // No additional methods needed - all abstract methods are already implemented in the parent
        };
    }

    public function testPersistenceIsCorrectlySetAndRetrieved(): void
    {
        // Assert the Persistence object set in constructor is the same as the one retrieved.
        $this->assertInstanceOf(Persistence::class, $this->sut->getPersistence());
    }

    public function testCreateDelegatesToWrappedPersistence(): void
    {
        $data = ['name' => 'Test'];
        $expectedResult = '123';

        // Configure the mock to expect the create method call and return a specific value.
        $this->persistenceMock->expects($this->once())
                              ->method('create')
                              ->with($data)
                              ->willReturn($expectedResult);

        // Assert the create method of the decorator returns what the wrapped persistence's create method returns.
        $this->assertSame($expectedResult, $this->sut->create($data), 'Create method should delegate to the wrapped persistence implementation.');
    }
}
